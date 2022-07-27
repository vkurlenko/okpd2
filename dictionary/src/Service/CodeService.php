<?php


namespace App\Service;


use App\Entity\Code;
use Symfony\Component\HttpFoundation\JsonResponse;


class CodeService
{
    /**
     * @param $doctrine
     * @param $xml
     * @return string
     */
    public function generateCodes($doctrine, $xml): string
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
     * @param $form
     * @param $codeRepository
     * @return Code
     */
    public function addCode($form, $codeRepository): Code
    {
        $code = new Code();
        $code->setName($form['name']);
        $code->setGlobalId(intval($form['global_id']));
        $code->setRazdel($form['razdel']);
        $code->setKod($form['kod']);
        $code->setNomdescr($form['nomdescr']);
        $code->setIdx($form['idx']);

        $codeRepository->add($code, true);

        return $code;
    }

    /**
     * @param $form
     * @param $doctrine
     * @return Code
     */
    public function updateCode($form, $doctrine): Code
    {
        $entityManager = $doctrine->getManager();

        $code = $entityManager->getRepository(Code::class)->find($form['id']);

        if ($code) {
            $code->setName($form['name']);
            $code->setGlobalId(intval($form['global_id']));
            $code->setRazdel($form['razdel']);
            $code->setKod($form['kod']);
            $code->setNomdescr($form['nomdescr']);
            $code->setIdx($form['idx']);

            $entityManager->flush();
        }

        return $code;
    }

    /**
     * @param $id
     * @param $doctrine
     * @param $codeRepository
     */
    public function deleteCode($id, $doctrine, $codeRepository)
    {
        $entityManager = $doctrine->getManager();
        $code = $entityManager->getRepository(Code::class)->find($id);

        $codeRepository->remove($code, true);
    }

    /**
     * @param $codeRepository
     * @return array
     */
    public function getTree($codeRepository): array
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

        return $tree;
    }

    /**
     * @param $level
     * @param $kod
     * @param $codeRepository
     * @return array
     */
    public function getChildren($level, $kod, $codeRepository): array
    {
        $pattern = $this->getPattern($level, $kod);
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
     * Returns a JSON response
     *
     * @param array $data
     * @param int $status
     * @param array $headers
     * @return JsonResponse
     */
    public function response(array $data, int $status = 200, array $headers = []): JsonResponse
    {
        return new JsonResponse($data, $status, $headers);
    }

    /**
     * @param $codeItem
     * @param $level
     * @return array
     */
    private function getCodeData($codeItem, $level): array
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

    /**
     * @param $level
     * @param $kod
     * @return string
     */
    private function getPattern($level, $kod): string
    {
        switch ($level) {
            case 1:
                $pattern = "'^".sprintf( '%02d', $kod ).".(\d)$'";
                break;
            case 2:
            case 4: $pattern = "'^".$kod."(\d)$'";
                break;
            case 3: $pattern = "'^".$kod.".(\d)$'";
                break;
            default: $pattern = "'^".$kod.".(\d\d\d)$'";
                break;
        }

        return $pattern;
    }
}