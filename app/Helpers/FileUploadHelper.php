<?php

namespace App\Helpers;

/**
 * File Upload Helper
 * Handles file uploads for profile photos and company logos
 */
class FileUploadHelper
{
    // Configuration constants
    private const MAX_FILE_SIZE = 2097152; // 2MB in bytes
    private const ALLOWED_TYPES = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

    // Upload directories
    private const PROFILE_PHOTO_DIR = WRITEPATH . 'uploads/profiles/';
    private const COMPANY_LOGO_DIR = WRITEPATH . 'uploads/logos/';

    // Image dimensions
    private const PROFILE_PHOTO_SIZE = 300; // Square 300x300
    private const LOGO_MAX_WIDTH = 500;
    private const LOGO_MAX_HEIGHT = 200;

    /**
     * Upload and process a profile photo
     */
    public static function uploadProfilePhoto($file, string $userId): array
    {
        // Validate file
        $validation = self::validateImageFile($file);
        if (!$validation['success']) {
            return $validation;
        }

        // Create directory if it doesn't exist
        if (!is_dir(self::PROFILE_PHOTO_DIR)) {
            mkdir(self::PROFILE_PHOTO_DIR, 0755, true);
        }

        // Generate unique filename
        $extension = pathinfo($file->getName(), PATHINFO_EXTENSION);
        $filename = 'profile_' . $userId . '_' . time() . '.' . $extension;
        $filepath = self::PROFILE_PHOTO_DIR . $filename;

        try {
            // Move uploaded file
            if (!$file->move(self::PROFILE_PHOTO_DIR, $filename)) {
                return ['success' => false, 'error' => 'Échec du téléchargement du fichier.'];
            }

            // Resize to square
            self::resizeImageSquare($filepath, self::PROFILE_PHOTO_SIZE);

            return [
                'success' => true,
                'filename' => $filename,
                'path' => 'uploads/profiles/' . $filename
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => 'Erreur lors du traitement de l\'image : ' . $e->getMessage()];
        }
    }

    /**
     * Upload and process a company logo
     */
    public static function uploadCompanyLogo($file, string $companyId): array
    {
        // Validate file
        $validation = self::validateImageFile($file);
        if (!$validation['success']) {
            return $validation;
        }

        // Create directory if it doesn't exist
        if (!is_dir(self::COMPANY_LOGO_DIR)) {
            mkdir(self::COMPANY_LOGO_DIR, 0755, true);
        }

        // Generate unique filename
        $extension = pathinfo($file->getName(), PATHINFO_EXTENSION);
        $filename = 'logo_' . $companyId . '_' . time() . '.' . $extension;
        $filepath = self::COMPANY_LOGO_DIR . $filename;

        try {
            // Move uploaded file
            if (!$file->move(self::COMPANY_LOGO_DIR, $filename)) {
                return ['success' => false, 'error' => 'Échec du téléchargement du fichier.'];
            }

            // Resize maintaining aspect ratio
            self::resizeImage($filepath, self::LOGO_MAX_WIDTH, self::LOGO_MAX_HEIGHT);

            return [
                'success' => true,
                'filename' => $filename,
                'path' => 'uploads/logos/' . $filename
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => 'Erreur lors du traitement de l\'image : ' . $e->getMessage()];
        }
    }

    /**
     * Validate image file
     */
    public static function validateImageFile($file): array
    {
        if (!$file || !$file->isValid()) {
            return ['success' => false, 'error' => 'Aucun fichier valide fourni.'];
        }

        // Check file size
        if ($file->getSize() > self::MAX_FILE_SIZE) {
            return ['success' => false, 'error' => 'Le fichier est trop volumineux. Taille maximum : 2MB.'];
        }

        // Check MIME type
        $mimeType = $file->getMimeType();
        if (!in_array($mimeType, self::ALLOWED_TYPES)) {
            return ['success' => false, 'error' => 'Type de fichier non autorisé. Formats acceptés : JPG, PNG, WebP.'];
        }

        // Check extension
        $extension = strtolower($file->getExtension());
        if (!in_array($extension, self::ALLOWED_EXTENSIONS)) {
            return ['success' => false, 'error' => 'Extension de fichier non autorisée.'];
        }

        return ['success' => true];
    }

    /**
     * Resize image to square dimensions
     */
    private static function resizeImageSquare(string $filepath, int $size): bool
    {
        $imageService = \Config\Services::image();

        try {
            $imageService->withFile($filepath)
                ->fit($size, $size, 'center')
                ->save($filepath);
            return true;
        } catch (\Exception $e) {
            log_message('error', 'Image resize failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Resize image maintaining aspect ratio
     */
    private static function resizeImage(string $filepath, int $maxWidth, int $maxHeight): bool
    {
        $imageService = \Config\Services::image();

        try {
            $imageService->withFile($filepath)
                ->resize($maxWidth, $maxHeight, true, 'auto')
                ->save($filepath);
            return true;
        } catch (\Exception $e) {
            log_message('error', 'Image resize failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a file safely
     */
    public static function deleteFile(string $path): bool
    {
        $fullPath = WRITEPATH . $path;

        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }

        return false;
    }

    /**
     * Get full URL for an uploaded file
     */
    public static function getFileUrl(string $path): string
    {
        return base_url($path);
    }

    /**
     * Check if file exists
     */
    public static function fileExists(string $path): bool
    {
        return file_exists(WRITEPATH . $path);
    }
}
