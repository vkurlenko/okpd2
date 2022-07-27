<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\CodeRepository as Repo;
use App\Service\CodeService as CodeService;

class FileUploader
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function upload($uploadDir, $file): string
    {
        if (empty($file))
        {
            return new Response("No file specified!",
                Response::HTTP_UNPROCESSABLE_ENTITY, ['content-type' => 'text/plain']);
        }

        $filename = $file->getClientOriginalName();

        try {
            if ($file->move($uploadDir, $filename)) {
                return $filename;
            };
        } catch (FileException $e){
            $this->logger->error('failed to upload file: ' . $e->getMessage());
            throw new FileException('Failed to upload file');
        }
    }
}