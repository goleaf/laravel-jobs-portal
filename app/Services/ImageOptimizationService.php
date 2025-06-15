<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ImageOptimizationService
{
    /**
     * Supported image formats.
     */
    public const SUPPORTED_FORMATS = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    /**
     * Responsive image sizes.
     */
    public const RESPONSIVE_SIZES = [
        'xs' => 320,   // Mobile
        'sm' => 640,   // Small tablet
        'md' => 768,   // Tablet
        'lg' => 1024,  // Desktop
        'xl' => 1280,  // Large desktop
        'xxl' => 1920,  // Ultra-wide
    ];

    /**
     * Image quality settings.
     */
    public const QUALITY_SETTINGS = [
        'low' => 60,
        'medium' => 75,
        'high' => 85,
        'ultra' => 95,
    ];

    /**
     * Convert image to WebP format using GD library.
     *
     * @return null|string WebP path or null on failure
     */
    public function convertToWebP(string $imagePath, int $quality = 85): ?string
    {
        try {
            if (!$this->isImageSupported($imagePath)) {
                Log::warning("Unsupported image format: {$imagePath}");

                return null;
            }

            $fullPath = Storage::path($imagePath);
            if (!file_exists($fullPath)) {
                Log::warning("Image file not found: {$fullPath}");

                return null;
            }

            $webpPath = $this->generateWebPPath($imagePath);
            $webpFullPath = Storage::path($webpPath);

            if (file_exists($webpFullPath)) {
                return $webpPath;
            }

            // Create image from source
            $sourceImage = $this->createImageFromFile($fullPath);
            if (!$sourceImage) {
                return null;
            }

            // Create directory if it doesn't exist
            $webpDir = dirname($webpFullPath);
            if (!is_dir($webpDir)) {
                mkdir($webpDir, 0755, true);
            }

            // Convert to WebP
            $success = imagewebp($sourceImage, $webpFullPath, $quality);
            imagedestroy($sourceImage);

            if ($success) {
                Log::info('Converted image to WebP', [
                    'original' => $imagePath,
                    'webp' => $webpPath,
                    'quality' => $quality,
                ]);

                return $webpPath;
            }

            return null;
        } catch (\Exception $e) {
            Log::error("WebP conversion failed for: {$imagePath}", [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Generate responsive image sizes.
     */
    public function generateResponsiveImages(string $imagePath, ?array $sizes = null, int $quality = 85): array
    {
        $sizes = $sizes ?? self::RESPONSIVE_SIZES;
        $responsiveImages = [];

        try {
            $fullPath = Storage::path($imagePath);
            if (!file_exists($fullPath)) {
                return [];
            }

            $sourceImage = $this->createImageFromFile($fullPath);
            if (!$sourceImage) {
                return [];
            }

            $originalWidth = imagesx($sourceImage);
            $originalHeight = imagesy($sourceImage);

            foreach ($sizes as $sizeName => $width) {
                // Skip if requested size is larger than original
                if ($width > $originalWidth) {
                    continue;
                }

                $responsivePath = $this->generateResponsivePath($imagePath, $sizeName, $width);
                $responsiveFullPath = Storage::path($responsivePath);

                if (!file_exists($responsiveFullPath)) {
                    // Calculate new height maintaining aspect ratio
                    $aspectRatio = $originalHeight / $originalWidth;
                    $newHeight = round($width * $aspectRatio);

                    // Create resized image
                    $resizedImage = imagecreatetruecolor($width, $newHeight);

                    // Handle transparency for PNG and GIF
                    $this->preserveTransparency($sourceImage, $resizedImage, $imagePath);

                    // Resize image
                    imagecopyresampled(
                        $resizedImage,
                        $sourceImage,
                        0,
                        0,
                        0,
                        0,
                        $width,
                        $newHeight,
                        $originalWidth,
                        $originalHeight
                    );

                    // Create directory if it doesn't exist
                    $responsiveDir = dirname($responsiveFullPath);
                    if (!is_dir($responsiveDir)) {
                        mkdir($responsiveDir, 0755, true);
                    }

                    // Save resized image
                    $this->saveImage($resizedImage, $responsiveFullPath, $imagePath, $quality);

                    // Generate WebP version
                    $webpPath = $this->generateResponsiveWebPPath($imagePath, $sizeName, $width);
                    $webpFullPath = Storage::path($webpPath);
                    if (!file_exists($webpFullPath)) {
                        imagewebp($resizedImage, $webpFullPath, $quality);
                    }

                    imagedestroy($resizedImage);
                }

                $responsiveImages[$sizeName] = [
                    'width' => $width,
                    'path' => $responsivePath,
                    'webp' => $this->generateResponsiveWebPPath($imagePath, $sizeName, $width),
                    'url' => Storage::url($responsivePath),
                    'webp_url' => Storage::url($this->generateResponsiveWebPPath($imagePath, $sizeName, $width)),
                ];
            }

            imagedestroy($sourceImage);

            Log::info("Generated responsive images for: {$imagePath}", [
                'sizes_generated' => count($responsiveImages),
            ]);
        } catch (\Exception $e) {
            Log::error("Responsive image generation failed for: {$imagePath}", [
                'error' => $e->getMessage(),
            ]);
        }

        return $responsiveImages;
    }

    /**
     * Optimize existing image.
     */
    public function optimizeImage(string $imagePath, string $quality = 'high'): bool
    {
        try {
            if (!$this->isImageSupported($imagePath)) {
                return false;
            }

            $qualityValue = self::QUALITY_SETTINGS[$quality] ?? self::QUALITY_SETTINGS['high'];

            $image = Image::make(Storage::path($imagePath));
            $optimizedContent = $image->stream(null, $qualityValue);

            Storage::put($imagePath, $optimizedContent);

            Log::info("Optimized image: {$imagePath}", [
                'quality' => $quality,
                'quality_value' => $qualityValue,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Image optimization failed for: {$imagePath}", [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Generate blur placeholder for progressive loading.
     *
     * @return null|string Base64 encoded blur placeholder
     */
    public function generateBlurPlaceholder(string $imagePath, int $width = 20, int $height = 20): ?string
    {
        try {
            $fullPath = Storage::path($imagePath);
            if (!file_exists($fullPath)) {
                return null;
            }

            $sourceImage = $this->createImageFromFile($fullPath);
            if (!$sourceImage) {
                return null;
            }

            // Create small placeholder
            $placeholder = imagecreatetruecolor($width, $height);
            imagecopyresampled(
                $placeholder,
                $sourceImage,
                0,
                0,
                0,
                0,
                $width,
                $height,
                imagesx($sourceImage),
                imagesy($sourceImage)
            );

            // Apply blur filter
            imagefilter($placeholder, IMG_FILTER_GAUSSIAN_BLUR);
            imagefilter($placeholder, IMG_FILTER_GAUSSIAN_BLUR);

            // Capture output
            ob_start();
            imagejpeg($placeholder, null, 60);
            $imageData = ob_get_contents();
            ob_end_clean();

            imagedestroy($sourceImage);
            imagedestroy($placeholder);

            $base64 = 'data:image/jpeg;base64,'.base64_encode($imageData);

            Log::debug("Generated blur placeholder for: {$imagePath}");

            return $base64;
        } catch (\Exception $e) {
            Log::error("Blur placeholder generation failed for: {$imagePath}", [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Get optimized image data for frontend.
     */
    public function getOptimizedImageData(string $imagePath, array $options = []): array
    {
        $options = array_merge([
            'generate_responsive' => true,
            'generate_webp' => true,
            'generate_placeholder' => true,
            'quality' => 'high',
        ], $options);

        $result = [
            'original' => [
                'path' => $imagePath,
                'url' => Storage::url($imagePath),
            ],
        ];

        // Generate WebP version
        if ($options['generate_webp']) {
            $webpPath = $this->convertToWebP($imagePath, self::QUALITY_SETTINGS[$options['quality']]);
            if ($webpPath) {
                $result['webp'] = [
                    'path' => $webpPath,
                    'url' => Storage::url($webpPath),
                ];
            }
        }

        // Generate responsive images
        if ($options['generate_responsive']) {
            $result['responsive'] = $this->generateResponsiveImages(
                $imagePath,
                null,
                self::QUALITY_SETTINGS[$options['quality']]
            );
        }

        // Generate blur placeholder
        if ($options['generate_placeholder']) {
            $result['placeholder'] = $this->generateBlurPlaceholder($imagePath);
        }

        return $result;
    }

    /**
     * Clean up generated image variants.
     */
    public function cleanupImageVariants(string $imagePath): bool
    {
        try {
            $baseDir = dirname($imagePath);
            $baseName = pathinfo($imagePath, PATHINFO_FILENAME);

            // Delete WebP version
            $webpPath = $this->generateWebPPath($imagePath);
            if (Storage::exists($webpPath)) {
                Storage::delete($webpPath);
            }

            // Delete responsive variants
            foreach (self::RESPONSIVE_SIZES as $sizeName => $width) {
                $responsivePath = $this->generateResponsivePath($imagePath, $sizeName, $width);
                $responsiveWebPPath = $this->generateResponsiveWebPPath($imagePath, $sizeName, $width);

                if (Storage::exists($responsivePath)) {
                    Storage::delete($responsivePath);
                }

                if (Storage::exists($responsiveWebPPath)) {
                    Storage::delete($responsiveWebPPath);
                }
            }

            Log::info("Cleaned up image variants for: {$imagePath}");

            return true;
        } catch (\Exception $e) {
            Log::error("Image variant cleanup failed for: {$imagePath}", [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get image information.
     */
    public function getImageInfo(string $imagePath): ?array
    {
        try {
            if (!Storage::exists($imagePath)) {
                return null;
            }

            $image = Image::make(Storage::path($imagePath));

            return [
                'width' => $image->width(),
                'height' => $image->height(),
                'mime' => $image->mime(),
                'filesize' => Storage::size($imagePath),
                'format' => $image->extension,
                'path' => $imagePath,
                'url' => Storage::url($imagePath),
            ];
        } catch (\Exception $e) {
            Log::error("Failed to get image info for: {$imagePath}", [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Create image resource from file.
     *
     * @return null|resource
     */
    protected function createImageFromFile(string $fullPath)
    {
        $imageInfo = getimagesize($fullPath);
        if (!$imageInfo) {
            return null;
        }

        switch ($imageInfo['mime']) {
            case 'image/jpeg':
                return imagecreatefromjpeg($fullPath);

            case 'image/png':
                return imagecreatefrompng($fullPath);

            case 'image/gif':
                return imagecreatefromgif($fullPath);

            case 'image/webp':
                return imagecreatefromwebp($fullPath);

            default:
                return null;
        }
    }

    /**
     * Preserve transparency for PNG and GIF images.
     *
     * @param resource $source
     * @param resource $destination
     */
    protected function preserveTransparency($source, $destination, string $imagePath): void
    {
        $extension = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));

        if ('png' === $extension) {
            imagealphablending($destination, false);
            imagesavealpha($destination, true);
            $transparent = imagecolorallocatealpha($destination, 255, 255, 255, 127);
            imagefill($destination, 0, 0, $transparent);
        } elseif ('gif' === $extension) {
            $transparentIndex = imagecolortransparent($source);
            if ($transparentIndex >= 0) {
                $transparentColor = imagecolorsforindex($source, $transparentIndex);
                $transparentIndex = imagecolorallocate(
                    $destination,
                    $transparentColor['red'],
                    $transparentColor['green'],
                    $transparentColor['blue']
                );
                imagefill($destination, 0, 0, $transparentIndex);
                imagecolortransparent($destination, $transparentIndex);
            }
        }
    }

    /**
     * Save image with appropriate format.
     *
     * @param resource $image
     */
    protected function saveImage($image, string $fullPath, string $originalPath, int $quality): void
    {
        $extension = strtolower(pathinfo($originalPath, PATHINFO_EXTENSION));

        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                imagejpeg($image, $fullPath, $quality);

                break;

            case 'png':
                // PNG quality is 0-9, convert from 0-100
                $pngQuality = round((100 - $quality) / 10);
                imagepng($image, $fullPath, $pngQuality);

                break;

            case 'gif':
                imagegif($image, $fullPath);

                break;

            case 'webp':
                imagewebp($image, $fullPath, $quality);

                break;
        }
    }

    /**
     * Check if image format is supported.
     */
    protected function isImageSupported(string $imagePath): bool
    {
        $extension = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));

        return in_array($extension, self::SUPPORTED_FORMATS);
    }

    /**
     * Generate WebP path for an image.
     */
    protected function generateWebPPath(string $imagePath): string
    {
        $pathInfo = pathinfo($imagePath);

        return $pathInfo['dirname'].'/'.$pathInfo['filename'].'.webp';
    }

    /**
     * Generate responsive image path.
     */
    protected function generateResponsivePath(string $imagePath, string $sizeName, int $width): string
    {
        $pathInfo = pathinfo($imagePath);

        return $pathInfo['dirname'].'/'.$pathInfo['filename']."_{$sizeName}_{$width}w.".$pathInfo['extension'];
    }

    /**
     * Generate responsive WebP path.
     */
    protected function generateResponsiveWebPPath(string $imagePath, string $sizeName, int $width): string
    {
        $pathInfo = pathinfo($imagePath);

        return $pathInfo['dirname'].'/'.$pathInfo['filename']."_{$sizeName}_{$width}w.webp";
    }
}
