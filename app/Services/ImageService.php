<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ImageService
{
    /**
     * Convert image to WebP format
     */
    public function convertToWebP(string $imagePath, int $quality = 85): ?string
    {
        try {
            $fullPath = Storage::path($imagePath);
            $webpPath = str_replace(pathinfo($imagePath, PATHINFO_EXTENSION), 'webp', $imagePath);
            $webpFullPath = Storage::path($webpPath);
            
            if (file_exists($webpFullPath)) {
                return $webpPath;
            }

            $image = $this->createImageFromFile($fullPath);
            if ($image && imagewebp($image, $webpFullPath, $quality)) {
                imagedestroy($image);
                return $webpPath;
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error("WebP conversion failed", ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Generate responsive images
     */
    public function generateResponsive(string $imagePath): array
    {
        $sizes = ['sm' => 640, 'md' => 768, 'lg' => 1024];
        $responsive = [];

        foreach ($sizes as $name => $width) {
            $responsivePath = $this->resizeImage($imagePath, $width);
            if ($responsivePath) {
                $responsive[$name] = [
                    'path' => $responsivePath,
                    'width' => $width,
                    'url' => Storage::url($responsivePath)
                ];
            }
        }

        return $responsive;
    }

    /**
     * Generate blur placeholder
     */
    public function generatePlaceholder(string $imagePath): ?string
    {
        try {
            $image = $this->createImageFromFile(Storage::path($imagePath));
            if (!$image) return null;

            $placeholder = imagecreatetruecolor(20, 20);
            imagecopyresampled($placeholder, $image, 0, 0, 0, 0, 20, 20, imagesx($image), imagesy($image));
            imagefilter($placeholder, IMG_FILTER_GAUSSIAN_BLUR);

            ob_start();
            imagejpeg($placeholder, null, 60);
            $data = ob_get_contents();
            ob_end_clean();

            imagedestroy($image);
            imagedestroy($placeholder);

            return 'data:image/jpeg;base64,' . base64_encode($data);
        } catch (\Exception $e) {
            return null;
        }
    }

    private function createImageFromFile(string $path)
    {
        $info = getimagesize($path);
        if (!$info) return null;

        switch ($info['mime']) {
            case 'image/jpeg': return imagecreatefromjpeg($path);
            case 'image/png': return imagecreatefrompng($path);
            case 'image/gif': return imagecreatefromgif($path);
            case 'image/webp': return imagecreatefromwebp($path);
            default: return null;
        }
    }

    private function resizeImage(string $imagePath, int $width): ?string
    {
        try {
            $pathInfo = pathinfo($imagePath);
            $resizedPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . "_{$width}w." . $pathInfo['extension'];
            
            if (Storage::exists($resizedPath)) {
                return $resizedPath;
            }

            $source = $this->createImageFromFile(Storage::path($imagePath));
            if (!$source) return null;

            $originalWidth = imagesx($source);
            $originalHeight = imagesy($source);
            $height = round($width * ($originalHeight / $originalWidth));

            $resized = imagecreatetruecolor($width, $height);
            imagecopyresampled($resized, $source, 0, 0, 0, 0, $width, $height, $originalWidth, $originalHeight);

            $resizedFullPath = Storage::path($resizedPath);
            imagejpeg($resized, $resizedFullPath, 85);

            imagedestroy($source);
            imagedestroy($resized);

            return $resizedPath;
        } catch (\Exception $e) {
            return null;
        }
    }
} 