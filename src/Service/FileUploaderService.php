<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class FileUploaderService
{
    private string $publicPath;

    public function __construct(ParameterBagInterface $params)
    {
        // Targets the /public folder in your Laragon directory
        $this->publicPath = $params->get('kernel.project_dir') . '/public/';
    }

    public function upload(
        UploadedFile $file,
        string $folder,
        string $baseFileName,
        string $randomSuffix
    ): ?array {
        $extension = strtolower($file->getClientOriginalExtension() ?: 'bin');
        $fileSize = $file->getSize();
        $mimeType = $file->getClientMimeType();
        $originalName = $file->getClientOriginalName();

        // 1. Sanitize the base filename (Peter's Logic)
        if (empty($baseFileName)) {
            $baseFileName = pathinfo($originalName, PATHINFO_FILENAME);
        }
        $baseFileName = str_replace([' ', '/'], '-', $baseFileName);

        // 2. Generate the final filename
        $fileName = $baseFileName . '_' . $randomSuffix . '.' . $extension;
        $relativePath = trim($folder, '/') . '/' . $fileName;
        $absolutePath = $this->publicPath . trim($folder, '/');

        // 3. Ensure the folder exists
        if (!is_dir($absolutePath)) {
            mkdir($absolutePath, 0755, true);
        }

        // 4. Move the file (equivalent to Laravel's move())
        try {
            $file->move($absolutePath, $fileName);
        } catch (\Exception $e) {
            // In a Sovereign system, we handle the error or return null
            return null;
        }

        return [
            'path'      => $relativePath,
            'original'  => $originalName,
            'mime'      => $mimeType,
            'size'      => $fileSize,
            'extension' => $extension,
            'filename'  => $fileName,
        ];
    }
}