<?php

namespace App\Controller;

use App\Service\FileUploader;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UploadController extends AbstractController
{
    /**
     * @Route("/doUpload", name="do-upload")
     * @param Request $request
     * @param string $uploadDir
     * @param FileUploader $uploader
     * @param LoggerInterface $logger
     * @return Response
     */
    public function index(Request $request, string $uploadDir,
                          FileUploader $uploader, LoggerInterface $logger): Response
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
//
//        $file = $request->files->get('myfile');
//
//        if (empty($file))
//        {
//            return new Response("No file specified",
//                Response::HTTP_UNPROCESSABLE_ENTITY, ['content-type' => 'text/plain']);
//        }
//
//        $filename = $file->getClientOriginalName();
//        $uploader->upload($uploadDir, $file, $filename);
//
//        if (file_exists($uploadDir.'/'.$filename)) {
//            $xml = simplexml_load_file($uploadDir.'/'.$filename);
////            dump($xml); die;
//        }

        return new Response("File uploaded",  Response::HTTP_OK,
            ['content-type' => 'text/plain']);
    }
}
