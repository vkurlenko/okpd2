<?php

namespace App\Controller;

use App\Entity\Code;
use App\Repository\CodeRepository;
use App\Service\FileUploader;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Integer;
use Psr\Log\LoggerInterface;
use SimpleXMLElement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CodeController extends AbstractController
{
    /**
     * @Route("/code", name="app_code")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
//        $tree = $this->getTree();

        return $this->render('code/index.html.twig', [
            'controller_name' => 'CodeController',
        ]);
    }

    /**
     * @Route("/uploader", name="uploader")
     * @param Request $request
     * @param string $uploadDir
     * @param FileUploader $uploader
     * @param LoggerInterface $logger
     * @return Response
     */
    public function upload(Request $request, string $uploadDir,
                           FileUploader $uploader, LoggerInterface $logger, ManagerRegistry $doctrine): Response
    {
//        $token = $request->get("token");
//
//        if (!$this->isCsrfTokenValid('upload', $token))
//        {
//            $logger->info("CSRF failure");
//
//            return new Response("Operation not allowed",  Response::HTTP_BAD_REQUEST,
//                ['content-type' => 'text/plain']);
//        }

        $file = $request->files->get('myfile');

        if (empty($file))
        {
            return new Response("No file specified!",
                Response::HTTP_UNPROCESSABLE_ENTITY, ['content-type' => 'text/plain']);
        }

        $filename = $file->getClientOriginalName();
        $uploader->upload($uploadDir, $file, $filename);

        if (file_exists($uploadDir.'/'.$filename)) {
            $xml = simplexml_load_file($uploadDir.'/'.$filename);
            $this->TruncateCodes($doctrine);
            $result = $this->createCode($doctrine, $xml);
//            echo "Saved $result codes";
            return $this->response(['data' => $result]);
        }

        return $this->render('code/index.html.twig', [
            'controller_name' => 'CodeController',
        ]);
    }

    public function createCode($doctrine, $xml): string
    {
        $entityManager = $doctrine->getManager();

        $i = 0;
        $count = 0;
        foreach ($xml as $item) {
            $i++;
            $code = new Code();
            $code->setName($item->Name);
            $code->setGlobalId(intval($item->global_id));
            $code->setRazdel($item->Razdel);
            if ($item->Kod) {
                $code->setKod($item->Kod);
            }
            $code->setNomdescr($item->Nomdescr);
            $code->setIdx($item->Idx);

            $entityManager->persist($code);
            unset($code);
            $count++;

            if ($i > 5000) {
                $entityManager->flush();
                $i = 0;
            }
        }

        $entityManager->flush();

        return $count;
    }

    /**
     * @Route("/add", name="add", methods={"POST"})
     */
    public function add(Request $request, CodeRepository $codeRepository)
    {
        $form = json_decode($request->getContent(), true);

        $code = new Code();
        $code->setName($form['name']);
        $code->setGlobalId(intval($form['global_id']));
        $code->setRazdel($form['razdel']);
        $code->setKod($form['kod']);
        $code->setNomdescr($form['nomdescr']);
        $code->setIdx($form['idx']);

        $codeRepository->add($code, true);

        return $this->response(['data' => $request]);
    }

    /**
     * @Route("/update", name="update", methods={"POST"})
     */
    public function update(Request $request, CodeRepository $codeRepository, ManagerRegistry $doctrine)
    {
        $form = json_decode($request->getContent(), true);

//        var_dump($form);

        $code = $codeRepository->find($form['id']);
        $code->setName($form['name']);
        $code->setGlobalId(intval($form['global_id']));
        $code->setRazdel($form['razdel']);
        $code->setKod($form['kod']);
        $code->setNomdescr($form['nomdescr']);
        $code->setIdx($form['idx']);

        $entityManager = $doctrine->getManager();
        $entityManager->flush();

        return $this->response(['data' => $request]);
    }

    public function TruncateCodes($doctrine)
    {
        $entityManager = $doctrine->getManager();
        $connection = $entityManager->getConnection();
        $platform   = $connection->getDatabasePlatform();

        $connection->executeUpdate($platform->getTruncateTableSQL('code', true));
    }

    /**
     * @Route("/tree", name="tree", methods={"GET"})
     */
    public function tree(CodeRepository $codeRepository)
    {
        $tree = $codeRepository->getCodeClasses();

        foreach ($tree as &$class) {
            $children = $this->getChildren(1, $class['kod'], $codeRepository);
            $class = [
                'children' => $children,
                'collapsedIcon' => "pi pi-folder",
                'data' => $this->getCodeData($class, 1),
                'expandedIcon' => "pi pi-folder-open",
                'label' => sprintf('%s %s', $class['kod'], $class['name'])
            ];
        }

        return $this->response(['data' => $tree]);
    }

    public function getChildren($level, $code, CodeRepository $codeRepository)
    {
        switch ($level) {
            case 1:
                $pattern = "'^".sprintf( '%02d', $code ).".(\d)$'";
                break;
            case 2:
            case 4: $pattern = "'^$code(\d)$'";
                break;
            case 3: $pattern = "'^$code.(\d)$'";
                break;
            default: $pattern = "'^$code.(\d\d\d)$'";
                break;
        }

        $children = $codeRepository->getChildren($pattern);
        foreach ($children as &$child) {
            $child = [
                'children' => [], //$this->getChildren($level + 1, $child['kod'], $codeRepository),
                'collapsedIcon' => "pi pi-folder",
                'data' => $this->getCodeData($child, $level + 1),
                'expandedIcon' => "pi pi-folder-open",
                'label' => sprintf('%s %s', $child['kod'], $child['name']),
            ];
        }

        return $children;
    }

    /**
     * @Route("/children", name="children")
     */
    public function children(Request $request, CodeRepository $codeRepository)
    {
        switch ($request->get('level')) {
            case 1:
                $pattern = "'^".sprintf( '%02d', $request->get('kod') ).".(\d)$'";
                break;
            case 2:
            case 4: $pattern = "'^".$request->get('kod')."(\d)$'";
                break;
            case 3: $pattern = "'^".$request->get('kod').".(\d)$'";
                break;
            default: $pattern = "'^".$request->get('kod').".(\d\d\d)$'";
                break;
        }

        $children = $codeRepository->getChildren($pattern);
        foreach ($children as &$child) {
            $child = [
                'children' => [],
                'collapsedIcon' => "pi pi-folder",
                'data' => $this->getCodeData($child, $request->get('level') + 1),
                'expandedIcon' => "pi pi-folder-open",
                'label' => sprintf('%s %s', $child['kod'], $child['name'])
            ];
        }

        return $this->response(['data' => $children]);
    }

    /**
     * @Route("/delete", name="delete")
     */
    public function delete(Request $request, CodeRepository $codeRepository)
    {
        $codeRepository->deleteNode($request->get('id'));

        return $this->response(['data' => $request->get('id')]);
    }

    /**
     * Returns a JSON response
     *
     * @param array $data
     * @param $status
     * @param array $headers
     * @return JsonResponse
     */
    public function response($data, $status = 200, $headers = []): JsonResponse
    {
        return new JsonResponse($data, $status, $headers);
    }

    protected function transformJsonBody(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            return $request;
        }

        $request->request->replace($data);

        return $request;
    }

    private function getCodeData($codeItem, $level)
    {
        return [
            "id" => $codeItem['id'],
            "name" => $codeItem['name'],
            "global_id" => $codeItem['global_id'],
            "razdel" => $codeItem['razdel'],
            "kod" => $codeItem['kod'],
            "nomdescr" => $codeItem['nomdescr'],
            "idx" => $codeItem['idx'],
            "deleted_at" => $codeItem['deleted_at'],
            'level' => $level
        ];
    }
}
