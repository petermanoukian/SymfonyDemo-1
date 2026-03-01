<?php

namespace App\Service;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ImageUploadService
{
    private string $publicPath;

    public function __construct(ParameterBagInterface $params)
    {
        // Points to your /public folder in Laragon
        $this->publicPath = $params->get('kernel.project_dir') . '/public/';
    }

    public function upload(
        UploadedFile $file,
        string $largeFolder,
        string $smallFolder,
        int $maxWidth = 1500,
        int $maxHeight = 1000,
        ?string $baseFileName = null,
        int $thumbWidth = 200,
        int $thumbHeight = 200
    ): ?array {
        $originalName = $file->getClientOriginalName();
        $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');

        // Sanitize name logic
        if (null === $baseFileName) {
            $baseFileName = pathinfo($originalName, PATHINFO_FILENAME);
        }
        $baseFileName = str_replace([' ', '/'], '-', $baseFileName);
        
        $fileName = $baseFileName . '-' . time() . '_' . uniqid() . '.' . $extension;

        $relativeLargePath = trim($largeFolder, '/') . '/' . $fileName;
        $relativeSmallPath = trim($smallFolder, '/') . '/' . $fileName;
        
        $absoluteLargePath = $this->publicPath . $relativeLargePath;
        $absoluteSmallPath = $this->publicPath . $relativeSmallPath;

        // Ensure directories exist
        $this->createDir(dirname($absoluteLargePath));
        $this->createDir(dirname($absoluteSmallPath));

        $imagine = new Imagine();
        
        // 1. Save & Resize Large Image (img)
        $image = $imagine->open($file->getPathname());
        $size = $image->getSize();
        
        if ($size->getWidth() > $maxWidth || $size->getHeight() > $maxHeight) {
            $ratio = min($maxWidth / $size->getWidth(), $maxHeight / $size->getHeight());
            $newSize = $size->scale($ratio);
            $image->resize($newSize);
        }
        $image->save($absoluteLargePath);

        // 2. Create Square Thumbnail (img2)
        $thumbnail = $imagine->open($absoluteLargePath);
        $thumbnail->thumbnail(new Box($thumbWidth, $thumbHeight), ImageInterface::THUMBNAIL_OUTBOUND)
                  ->save($absoluteSmallPath);

        return [
            'large'         => $relativeLargePath,
            'small'         => $relativeSmallPath,
            'original_name' => $originalName,
        ];
    }

    private function createDir(string $path): void
    {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }
}