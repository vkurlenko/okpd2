<?php

namespace App\Controller;

use App\Repository\CodeRepository;
use App\Service\CodeService;
use App\Service\FileUploader;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CodeController extends AbstractController
{
    /**
     * @Route("/code", name="app_code")
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('code/index.html.twig', [
            'controller_name' => 'CodeController',
        ]);
    }

    /**
     * @Route("/uploader", name="uploader")
     * @param Request $request
     * @param string $uploadDir
     * @param FileUploader $uploader
     * @param ManagerRegistry $doctrine
     * @param CodeRepository $codeRepository
     * @param CodeService $codeService
     * @return Response
     */
    public function upload(Request $request, string $uploadDir, FileUploader $uploader,
                           ManagerRegistry $doctrine, CodeRepository $codeRepository,
                           CodeService $codeService): Response
    {
        $file = $request->files->get('myfile');
        $filename = $uploader->upload($uploadDir, $file);

        if (file_exists($uploadDir.'/'.$filename)) {
            $xml = simplexml_load_file($uploadDir.'/'.$filename);
            $codeRepository->truncateCodes();
            $result = $codeService->generateCodes($doctrine, $xml);

            return $codeService->response(['data' => $result]);
        }

        return $this->render('code/index.html.twig', [
            'controller_name' => 'CodeController',
        ]);
    }

    /**
     * @Route("/add", name="add", methods={"POST"})
     * @param Request $request
     * @param CodeRepository $codeRepository
     * @param CodeService $codeService
     * @return Response
     */
    public function add(Request $request, CodeRepository $codeRepository, CodeService $codeService): Response
    {
        $form = json_decode($request->getContent(), true);
        $code = $codeService->addCode($form, $codeRepository);

        return $codeService->response(['data' => $code]);
    }

    /**
     * @Route("/update", name="update", methods={"POST"})
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @param CodeService $codeService
     * @return Response
     */
    public function update(Request $request, ManagerRegistry $doctrine, CodeService $codeService): Response
    {
        $form = json_decode($request->getContent(), true);
        $code = $codeService->updateCode($form, $doctrine);

        return $codeService->response(['data' => $code]);
    }

    /**
     * @Route("/delete", name="delete")
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @param CodeRepository $codeRepository
     * @param CodeService $codeService
     * @return Response
     */
    public function delete(Request $request, ManagerRegistry $doctrine, CodeRepository $codeRepository,
                           CodeService $codeService): Response
    {
        $codeService->deleteCode($request->get('id'), $doctrine, $codeRepository);

        return $codeService->response(['data' => $request->get('id')]);
    }

    /**
     * @Route("/tree", name="tree", methods={"GET"})
     * @param CodeRepository $codeRepository
     * @param CodeService $codeService
     * @return Response
     */
    public function tree(CodeRepository $codeRepository, CodeService $codeService): Response
    {
        $tree = $codeService->getTree($codeRepository);

        return $codeService->response(['data' => $tree]);
    }

    /**
     * @Route("/children", name="children")
     * @param Request $request
     * @param CodeRepository $codeRepository
     * @param CodeService $codeService
     * @return Response
     */
    public function children(Request $request, CodeRepository $codeRepository, CodeService $codeService): Response
    {
        $children = $codeService->getChildren($request->get('level'), $request->get('kod'), $codeRepository);

        return $codeService->response(['data' => $children]);
    }
}
