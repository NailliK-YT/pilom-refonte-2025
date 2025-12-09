#!/usr/bin/env php
<?php

/**
 * WebP Image Conversion Script
 * 
 * Usage: php scripts/webp_convert.php [options]
 * 
 * Options:
 *   --source=DIR    Source directory (default: public/assets/images)
 *   --quality=N     WebP quality 0-100 (default: 80)
 *   --dry-run       Show what would be converted without actually converting
 *   --force         Reconvert even if WebP exists
 */

// Bootstrap CodeIgniter
$minPHPVersion = '8.1';
if (version_compare(PHP_VERSION, $minPHPVersion, '<')) {
    die("PHP {$minPHPVersion} or newer is required. Current version: " . PHP_VERSION);
}

// Set paths
define('FCPATH', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);

// Parse command line arguments
$options = getopt('', ['source:', 'quality:', 'dry-run', 'force', 'help']);

if (isset($options['help'])) {
    echo <<<HELP
WebP Image Conversion Script for Pilom

Usage: php scripts/webp_convert.php [options]

Options:
  --source=DIR    Source directory relative to public/ (default: assets/images)
  --quality=N     WebP quality 0-100 (default: 80)
  --dry-run       Show what would be converted without actually converting
  --force         Reconvert even if WebP exists and is up to date
  --help          Show this help message

Examples:
  php scripts/webp_convert.php
  php scripts/webp_convert.php --source=assets/images --quality=85
  php scripts/webp_convert.php --dry-run

HELP;
    exit(0);
}

// Configuration
$sourceDir = FCPATH . ($options['source'] ?? 'assets/images');
$quality = isset($options['quality']) ? (int) $options['quality'] : 80;
$dryRun = isset($options['dry-run']);
$force = isset($options['force']);

// Check GD support
if (!extension_loaded('gd')) {
    die("Error: GD extension is not loaded.\n");
}

$gdInfo = gd_info();
if (empty($gdInfo['WebP Support'])) {
    die("Error: GD extension does not support WebP.\n");
}

echo "=== WebP Image Conversion Tool ===\n\n";
echo "Source directory: {$sourceDir}\n";
echo "Quality: {$quality}\n";
echo "Mode: " . ($dryRun ? "DRY RUN" : "LIVE") . "\n\n";

if (!is_dir($sourceDir)) {
    die("Error: Source directory not found: {$sourceDir}\n");
}

// Find all images
$supportedFormats = ['png', 'jpg', 'jpeg', 'gif'];
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($sourceDir, FilesystemIterator::SKIP_DOTS)
);

$toConvert = [];
$skipped = 0;

foreach ($iterator as $file) {
    if ($file->isDir())
        continue;

    $extension = strtolower($file->getExtension());
    if (!in_array($extension, $supportedFormats))
        continue;

    $sourcePath = $file->getPathname();
    $webpPath = preg_replace('/\.(png|jpe?g|gif)$/i', '.webp', $sourcePath);

    // Skip if WebP exists and is newer (unless force)
    if (!$force && file_exists($webpPath) && filemtime($webpPath) >= filemtime($sourcePath)) {
        $skipped++;
        continue;
    }

    $toConvert[] = [
        'source' => $sourcePath,
        'webp' => $webpPath,
        'size' => $file->getSize()
    ];
}

echo "Found " . count($toConvert) . " image(s) to convert\n";
echo "Skipped {$skipped} image(s) (already converted)\n\n";

if (count($toConvert) === 0) {
    echo "Nothing to do!\n";
    exit(0);
}

// Convert images
$success = 0;
$failed = 0;
$totalSaved = 0;

foreach ($toConvert as $item) {
    $relativePath = str_replace(FCPATH, '', $item['source']);

    if ($dryRun) {
        echo "[DRY RUN] Would convert: {$relativePath}\n";
        $success++;
        continue;
    }

    echo "Converting: {$relativePath}... ";

    // Get image info
    $imageInfo = @getimagesize($item['source']);
    if ($imageInfo === false) {
        echo "FAILED (cannot read)\n";
        $failed++;
        continue;
    }

    $mimeType = $imageInfo['mime'];

    // Create image resource
    $image = match ($mimeType) {
        'image/png' => @imagecreatefrompng($item['source']),
        'image/jpeg' => @imagecreatefromjpeg($item['source']),
        'image/gif' => @imagecreatefromgif($item['source']),
        default => null
    };

    if ($image === null || $image === false) {
        echo "FAILED (unsupported format)\n";
        $failed++;
        continue;
    }

    // Handle transparency for PNG
    if ($mimeType === 'image/png') {
        imagepalettetotruecolor($image);
        imagealphablending($image, true);
        imagesavealpha($image, true);
    }

    // Convert to WebP
    $result = @imagewebp($image, $item['webp'], $quality);
    imagedestroy($image);

    if (!$result) {
        echo "FAILED (conversion error)\n";
        $failed++;
        continue;
    }

    $originalSize = $item['size'];
    $webpSize = filesize($item['webp']);
    $saved = $originalSize - $webpSize;
    $savedPercent = round(($saved / $originalSize) * 100, 1);
    $totalSaved += $saved;

    echo "OK (saved {$savedPercent}%)\n";
    $success++;
}

echo "\n=== Summary ===\n";
echo "Converted: {$success}\n";
echo "Failed: {$failed}\n";

if (!$dryRun && $totalSaved > 0) {
    $totalSavedKB = round($totalSaved / 1024, 2);
    echo "Total space saved: {$totalSavedKB} KB\n";
}

echo "\nDone!\n";
