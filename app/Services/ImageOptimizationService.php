<?php

namespace App\Services;

/**
 * Image Optimization Service
 * 
 * Converts images to WebP format and manages optimized versions.
 * Uses PHP GD extension for image processing.
 */
class ImageOptimizationService
{
    /**
     * WebP quality (0-100, higher = better quality but larger file)
     */
    protected int $webpQuality = 80;

    /**
     * Supported input formats
     */
    protected array $supportedFormats = ['png', 'jpg', 'jpeg', 'gif'];

    /**
     * Check if WebP is supported by the browser
     * 
     * @return bool
     */
    public function supportsWebp(): bool
    {
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        return strpos($accept, 'image/webp') !== false;
    }

    /**
     * Check if GD extension supports WebP
     * 
     * @return bool
     */
    public function canConvertWebp(): bool
    {
        if (!extension_loaded('gd')) {
            return false;
        }

        $gdInfo = gd_info();
        return !empty($gdInfo['WebP Support']);
    }

    /**
     * Convert an image to WebP format
     * 
     * @param string $sourcePath Full path to source image
     * @param string|null $destPath Destination path (null = same location with .webp)
     * @return string|null Path to WebP file or null on failure
     */
    public function convertToWebp(string $sourcePath, ?string $destPath = null): ?string
    {
        if (!$this->canConvertWebp()) {
            log_message('warning', 'ImageOptimization: WebP conversion not supported by GD');
            return null;
        }

        if (!file_exists($sourcePath)) {
            log_message('error', "ImageOptimization: Source file not found: {$sourcePath}");
            return null;
        }

        // Determine destination path
        if ($destPath === null) {
            $destPath = preg_replace('/\.(png|jpe?g|gif)$/i', '.webp', $sourcePath);
        }

        // Get image info
        $imageInfo = @getimagesize($sourcePath);
        if ($imageInfo === false) {
            log_message('error', "ImageOptimization: Cannot read image info: {$sourcePath}");
            return null;
        }

        $mimeType = $imageInfo['mime'];

        // Create image resource based on type
        $image = match ($mimeType) {
            'image/png' => @imagecreatefrompng($sourcePath),
            'image/jpeg' => @imagecreatefromjpeg($sourcePath),
            'image/gif' => @imagecreatefromgif($sourcePath),
            default => null
        };

        if ($image === null || $image === false) {
            log_message('error', "ImageOptimization: Cannot create image from: {$sourcePath}");
            return null;
        }

        // Handle transparency for PNG
        if ($mimeType === 'image/png') {
            imagepalettetotruecolor($image);
            imagealphablending($image, true);
            imagesavealpha($image, true);
        }

        // Convert to WebP
        $result = @imagewebp($image, $destPath, $this->webpQuality);
        imagedestroy($image);

        if (!$result) {
            log_message('error', "ImageOptimization: WebP conversion failed: {$sourcePath}");
            return null;
        }

        log_message('info', "ImageOptimization: Converted {$sourcePath} to WebP");
        return $destPath;
    }

    /**
     * Convert all images in a directory to WebP
     * 
     * @param string $directory Directory path
     * @param bool $recursive Process subdirectories
     * @return array Results with success and failed counts
     */
    public function convertDirectory(string $directory, bool $recursive = true): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'skipped' => 0,
            'files' => []
        ];

        if (!is_dir($directory)) {
            log_message('error', "ImageOptimization: Directory not found: {$directory}");
            return $results;
        }

        $flags = $recursive ? \FilesystemIterator::SKIP_DOTS : 0;
        $iterator = $recursive
            ? new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory, $flags))
            : new \DirectoryIterator($directory);

        foreach ($iterator as $file) {
            if ($file->isDir()) {
                continue;
            }

            $extension = strtolower($file->getExtension());

            if (!in_array($extension, $this->supportedFormats)) {
                continue;
            }

            $sourcePath = $file->getPathname();
            $webpPath = preg_replace('/\.(png|jpe?g|gif)$/i', '.webp', $sourcePath);

            // Skip if WebP already exists and is newer
            if (file_exists($webpPath) && filemtime($webpPath) >= filemtime($sourcePath)) {
                $results['skipped']++;
                continue;
            }

            $result = $this->convertToWebp($sourcePath);

            if ($result !== null) {
                $results['success']++;
                $results['files'][] = [
                    'source' => $sourcePath,
                    'webp' => $result,
                    'savings' => $this->calculateSavings($sourcePath, $result)
                ];
            } else {
                $results['failed']++;
            }
        }

        return $results;
    }

    /**
     * Calculate size savings percentage
     * 
     * @param string $originalPath Original file path
     * @param string $webpPath WebP file path
     * @return float Percentage saved (0-100)
     */
    protected function calculateSavings(string $originalPath, string $webpPath): float
    {
        $originalSize = filesize($originalPath);
        $webpSize = filesize($webpPath);

        if ($originalSize <= 0) {
            return 0;
        }

        return round((1 - ($webpSize / $originalSize)) * 100, 2);
    }

    /**
     * Get the optimized image URL (WebP if supported, original otherwise)
     * 
     * @param string $imagePath Relative path to image
     * @return string Optimized image URL
     */
    public function getOptimizedUrl(string $imagePath): string
    {
        if (!$this->supportsWebp()) {
            return base_url($imagePath);
        }

        $webpPath = preg_replace('/\.(png|jpe?g|gif)$/i', '.webp', $imagePath);
        $webpFullPath = FCPATH . ltrim($webpPath, '/');

        if (file_exists($webpFullPath)) {
            return base_url($webpPath);
        }

        return base_url($imagePath);
    }

    /**
     * Set WebP quality
     * 
     * @param int $quality Quality 0-100
     * @return self
     */
    public function setQuality(int $quality): self
    {
        $this->webpQuality = max(0, min(100, $quality));
        return $this;
    }
}
