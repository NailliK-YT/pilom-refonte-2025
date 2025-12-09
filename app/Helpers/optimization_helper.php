<?php

/**
 * Optimization Helper
 * 
 * Provides functions for lazy loading, WebP support, and asset versioning.
 * WCAG 2.1 AA compliant with accessibility attributes.
 */

if (!function_exists('optimized_image')) {
    /**
     * Generate an optimized image tag with lazy loading and WebP support
     * 
     * @param string $src Image source path (relative to base_url)
     * @param string $alt Alt text for accessibility (WCAG 2.1 AA required)
     * @param array $attrs Additional HTML attributes
     * @return string HTML picture/img element
     */
    function optimized_image(string $src, string $alt, array $attrs = []): string
    {
        $fullPath = FCPATH . ltrim($src, '/');
        $webpPath = preg_replace('/\.(png|jpg|jpeg|gif)$/i', '.webp', $src);
        $webpFullPath = FCPATH . ltrim($webpPath, '/');

        // Check if WebP version exists
        $hasWebp = file_exists($webpFullPath);

        // Default attributes
        $defaultAttrs = [
            'loading' => 'lazy',
            'decoding' => 'async',
        ];

        // Merge with custom attributes
        $finalAttrs = array_merge($defaultAttrs, $attrs);

        // Build attribute string
        $attrString = '';
        foreach ($finalAttrs as $key => $value) {
            if ($value !== null && $value !== false) {
                $attrString .= ' ' . esc($key) . '="' . esc($value) . '"';
            }
        }

        // Generate versioned URL
        $srcUrl = asset_url($src);
        $webpUrl = $hasWebp ? asset_url($webpPath) : null;

        // If WebP exists, use picture element for better support
        if ($hasWebp) {
            return sprintf(
                '<picture>
                    <source srcset="%s" type="image/webp">
                    <img src="%s" alt="%s"%s>
                </picture>',
                esc($webpUrl),
                esc($srcUrl),
                esc($alt),
                $attrString
            );
        }

        // Fallback to simple img
        return sprintf(
            '<img src="%s" alt="%s"%s>',
            esc($srcUrl),
            esc($alt),
            $attrString
        );
    }
}

if (!function_exists('eager_image')) {
    /**
     * Generate an optimized image WITHOUT lazy loading (for LCP elements)
     * Use this for images above the fold
     * 
     * @param string $src Image source path
     * @param string $alt Alt text for accessibility
     * @param array $attrs Additional HTML attributes
     * @return string HTML picture/img element
     */
    function eager_image(string $src, string $alt, array $attrs = []): string
    {
        $attrs['loading'] = 'eager';
        $attrs['fetchpriority'] = 'high';
        return optimized_image($src, $alt, $attrs);
    }
}

if (!function_exists('asset_url')) {
    /**
     * Generate a versioned asset URL for cache busting
     * 
     * @param string $path Asset path relative to public folder
     * @return string Full URL with version query parameter
     */
    function asset_url(string $path): string
    {
        $fullPath = FCPATH . ltrim($path, '/');

        // In production, use minified version if available
        if (ENVIRONMENT === 'production') {
            $minPath = get_minified_path($path);
            $minFullPath = FCPATH . ltrim($minPath, '/');

            if (file_exists($minFullPath)) {
                $path = $minPath;
                $fullPath = $minFullPath;
            }
        }

        // Add version based on file modification time
        $version = file_exists($fullPath) ? filemtime($fullPath) : time();

        return base_url($path) . '?v=' . $version;
    }
}

if (!function_exists('get_minified_path')) {
    /**
     * Get the minified version path for an asset
     * 
     * @param string $path Original asset path
     * @return string Minified asset path
     */
    function get_minified_path(string $path): string
    {
        // Convert css/style.css to dist/css/style.min.css
        if (preg_match('/^(css|js)\/(.+)\.(css|js)$/', $path, $matches)) {
            return 'dist/' . $matches[1] . '/' . $matches[2] . '.min.' . $matches[3];
        }
        return $path;
    }
}

if (!function_exists('css_link')) {
    /**
     * Generate a CSS link tag with versioning
     * 
     * @param string $file CSS filename (without path, e.g., 'dashboard.css')
     * @param array $attrs Additional attributes
     * @return string HTML link element
     */
    function css_link(string $file, array $attrs = []): string
    {
        $path = 'css/' . $file;
        $url = asset_url($path);

        $attrString = '';
        foreach ($attrs as $key => $value) {
            $attrString .= ' ' . esc($key) . '="' . esc($value) . '"';
        }

        return sprintf('<link rel="stylesheet" href="%s"%s>', esc($url), $attrString);
    }
}

if (!function_exists('js_script')) {
    /**
     * Generate a JavaScript script tag with versioning
     * 
     * @param string $file JS filename (without path, e.g., 'profile.js')
     * @param bool $defer Add defer attribute
     * @param array $attrs Additional attributes
     * @return string HTML script element
     */
    function js_script(string $file, bool $defer = true, array $attrs = []): string
    {
        $path = 'js/' . $file;
        $url = asset_url($path);

        $deferAttr = $defer ? ' defer' : '';

        $attrString = '';
        foreach ($attrs as $key => $value) {
            $attrString .= ' ' . esc($key) . '="' . esc($value) . '"';
        }

        return sprintf('<script src="%s"%s%s></script>', esc($url), $deferAttr, $attrString);
    }
}

if (!function_exists('preload_font')) {
    /**
     * Generate a preload link for fonts (improves FCP)
     * 
     * @param string $url Font URL
     * @param string $type Font type (woff2, woff, etc.)
     * @return string HTML link element for preloading
     */
    function preload_font(string $url, string $type = 'woff2'): string
    {
        return sprintf(
            '<link rel="preload" href="%s" as="font" type="font/%s" crossorigin>',
            esc($url),
            esc($type)
        );
    }
}

if (!function_exists('critical_css')) {
    /**
     * Generate inline critical CSS for above-the-fold content
     * 
     * @param string $css Critical CSS content
     * @return string HTML style element
     */
    function critical_css(string $css): string
    {
        return '<style>' . $css . '</style>';
    }
}
