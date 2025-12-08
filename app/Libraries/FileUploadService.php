<?php

namespace App\Libraries;

/**
 * File Upload Service
 * 
 * Handles file uploads, compression, and management for expense justificatifs
 */
class FileUploadService
{
    protected string $uploadBasePath;
    protected array $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];
    protected int $maxFileSize = 5242880; // 5MB in bytes

    public function __construct()
    {
        $this->uploadBasePath = WRITEPATH . 'uploads/depenses/';

        // Ensure base directory exists
        if (!is_dir($this->uploadBasePath)) {
            mkdir($this->uploadBasePath, 0755, true);
        }
    }

    /**
     * Upload justificatif file
     * 
     * @param \CodeIgniter\HTTP\Files\UploadedFile $file Uploaded file
     * @return array Result with success status, path, and error message
     */
    public function uploadJustificatif($file): array
    {
        // Validate file
        if (!$file || !$file->isValid()) {
            return [
                'success' => false,
                'error' => 'Fichier invalide ou non reçu'
            ];
        }

        // Check file size
        if ($file->getSize() > $this->maxFileSize) {
            return [
                'success' => false,
                'error' => 'Le fichier est trop volumineux (max 5MB)'
            ];
        }

        // Check extension
        $extension = strtolower($file->getExtension());
        if (!in_array($extension, $this->allowedExtensions)) {
            return [
                'success' => false,
                'error' => 'Type de fichier non autorisé. Formats acceptés: ' . implode(', ', $this->allowedExtensions)
            ];
        }

        try {
            // Create subdirectories by year/month
            $year = date('Y');
            $month = date('m');
            $uploadPath = $this->uploadBasePath . "$year/$month/";

            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Generate unique filename
            $newName = $this->generateUUID() . '.' . $extension;

            // Move file
            $file->move($uploadPath, $newName);

            $fullPath = $uploadPath . $newName;
            $relativePath = "$year/$month/$newName";

            // Compress file if applicable
            $this->compressFile($fullPath, $extension);

            // Log upload
            log_message('info', 'Justificatif uploaded: ' . $relativePath);

            return [
                'success' => true,
                'path' => $relativePath,
                'full_path' => $fullPath,
                'size' => filesize($fullPath)
            ];

        } catch (\Exception $e) {
            log_message('error', 'Upload error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Erreur lors de l\'upload du fichier'
            ];
        }
    }

    /**
     * Compress file (images and PDFs)
     * 
     * @param string $path Absolute path to file
     * @param string $extension File extension
     * @return bool Success status
     */
    public function compressFile(string $path, string $extension): bool
    {
        try {
            if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
                return $this->compressImage($path, $extension);
            } elseif ($extension === 'pdf') {
                // PDF compression would require external library like Ghostscript
                // For now, we'll just log it
                log_message('info', 'PDF compression skipped for: ' . $path);
                return true;
            }

            return true;
        } catch (\Exception $e) {
            log_message('error', 'Compression error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Compress image file
```php
     * 
     * @param string $path Absolute path to image
     * @param string $extension Image extension
     * @return bool Success status
     */
    protected function compressImage(string $path, string $extension): bool
    {
        try {
            $imageService = \Config\Services::image('gd');

            // Get image dimensions
            list($width, $height) = getimagesize($path);

            // Only compress if image is larger than 1600px in any dimension
            if ($width > 1600 || $height > 1600) {
                $imageService->withFile($path)
                    ->resize(1600, 1600, true, 'auto')
                    ->save($path, 85); // 85% quality

                log_message('info', 'Image compressed: ' . $path);
            }

            return true;
        } catch (\Exception $e) {
            log_message('error', 'Image compression error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate thumbnail for image
     * 
     * @param string $path Absolute path to image
     * @return string|null Path to thumbnail or null on failure
     */
    public function generateThumbnail(string $path): ?string
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if (!in_array($extension, ['jpg', 'jpeg', 'png'])) {
            return null;
        }

        try {
            $imageService = \Config\Services::image('gd');
            $thumbnailPath = str_replace('.' . $extension, '_thumb.' . $extension, $path);

            $imageService->withFile($path)
                ->fit(200, 200, 'center')
                ->save($thumbnailPath, 80);

            return $thumbnailPath;
        } catch (\Exception $e) {
            log_message('error', 'Thumbnail generation error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Delete justificatif file
     * 
     * @param string $relativePath Relative path from uploads/depenses/
     * @return bool Success status
     */
    public function deleteJustificatif(string $relativePath): bool
    {
        $fullPath = $this->uploadBasePath . $relativePath;

        if (!file_exists($fullPath)) {
            log_message('warning', 'Attempted to delete non-existent file: ' . $fullPath);
            return false;
        }

        try {
            // Delete thumbnail if exists
            $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
            if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
                $thumbnailPath = str_replace('.' . $extension, '_thumb.' . $extension, $fullPath);
                if (file_exists($thumbnailPath)) {
                    unlink($thumbnailPath);
                }
            }

            // Delete main file
            $result = unlink($fullPath);

            if ($result) {
                log_message('info', 'Justificatif deleted: ' . $relativePath);
            }

            return $result;
        } catch (\Exception $e) {
            log_message('error', 'File deletion error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get file info
     * 
     * @param string $relativePath Relative path from uploads/depenses/
     * @return array|null File info or null if not found
     */
    public function getFileInfo(string $relativePath): ?array
    {
        $fullPath = $this->uploadBasePath . $relativePath;

        if (!file_exists($fullPath)) {
            return null;
        }

        return [
            'path' => $relativePath,
            'full_path' => $fullPath,
            'size' => filesize($fullPath),
            'extension' => pathinfo($fullPath, PATHINFO_EXTENSION),
            'mime_type' => mime_content_type($fullPath),
            'modified' => filemtime($fullPath)
        ];
    }

    /**
     * Validate file type by checking MIME type
     * 
     * @param string $path Absolute path to file
     * @return bool True if valid
     */
    public function validateFileType(string $path): bool
    {
        $allowedMimes = [
            'application/pdf',
            'image/jpeg',
            'image/jpg',
            'image/png'
        ];

        $mimeType = mime_content_type($path);
        return in_array($mimeType, $allowedMimes);
    }

    /**
     * Generate UUID v4
     */
    protected function generateUUID(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    /**
     * Clean up old files (optional maintenance task)
     * 
     * @param int $days Delete files older than X days
     * @return int Number of deleted files
     */
    public function cleanupOldFiles(int $days = 365): int
    {
        $count = 0;
        $cutoffTime = time() - ($days * 86400);

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->uploadBasePath),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getMTime() < $cutoffTime) {
                try {
                    unlink($file->getPathname());
                    $count++;
                } catch (\Exception $e) {
                    log_message('error', 'Cleanup error: ' . $e->getMessage());
                }
            }
        }

        log_message('info', "Cleaned up $count old justificatif files");
        return $count;
    }
}
