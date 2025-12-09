<?php

/**
 * SEO Helper Functions
 * 
 * Helper functions for SEO optimization
 * Extended with additional Schema.org and meta tag utilities
 */

use App\Services\SeoService;

if (!function_exists('seo_service')) {
    /**
     * Get the SeoService instance
     * 
     * @return SeoService
     */
    function seo_service(): SeoService
    {
        static $service = null;
        if ($service === null) {
            $service = new SeoService();
        }
        return $service;
    }
}

if (!function_exists('seo_tags')) {
    /**
     * Generate all SEO tags (meta, OG, Twitter) using the SeoService
     * 
     * @param array $data SEO data array
     * @return string HTML output
     */
    function seo_tags(array $data = []): string
    {
        return seo_service()->generateAllTags($data);
    }
}

if (!function_exists('seo_title')) {
    /**
     * Generate an optimized SEO title (50-60 characters)
     * 
     * @param string $title Base title
     * @param bool $includeSiteName Include site name
     * @return string Optimized title
     */
    function seo_title(string $title, bool $includeSiteName = true): string
    {
        return seo_service()->generateTitle($title, $includeSiteName);
    }
}

if (!function_exists('seo_description')) {
    /**
     * Generate an optimized SEO description (150-160 characters)
     * 
     * @param string $description Base description
     * @return string Optimized description
     */
    function seo_description(string $description): string
    {
        return seo_service()->generateDescription($description);
    }
}

if (!function_exists('seo_meta')) {
    /**
     * Generate meta tags for SEO (legacy function - kept for backward compatibility)
     */
    function seo_meta(string $title, string $description, ?string $image = null, ?string $url = null): string
    {
        $output = '';
        $siteName = 'Pilom - Logiciel de Facturation';
        $fullTitle = $title . ' | ' . $siteName;
        $currentUrl = $url ?? current_url();
        $defaultImage = base_url('images/og-image.jpg');

        // Basic Meta
        $output .= '<meta name="description" content="' . esc($description) . '">' . "\n";
        $output .= '<meta name="robots" content="index, follow">' . "\n";

        // Open Graph
        $output .= '<meta property="og:type" content="website">' . "\n";
        $output .= '<meta property="og:title" content="' . esc($fullTitle) . '">' . "\n";
        $output .= '<meta property="og:description" content="' . esc($description) . '">' . "\n";
        $output .= '<meta property="og:url" content="' . esc($currentUrl) . '">' . "\n";
        $output .= '<meta property="og:image" content="' . esc($image ?? $defaultImage) . '">' . "\n";
        $output .= '<meta property="og:site_name" content="' . esc($siteName) . '">' . "\n";

        // Twitter Card
        $output .= '<meta name="twitter:card" content="summary_large_image">' . "\n";
        $output .= '<meta name="twitter:title" content="' . esc($fullTitle) . '">' . "\n";
        $output .= '<meta name="twitter:description" content="' . esc($description) . '">' . "\n";
        $output .= '<meta name="twitter:image" content="' . esc($image ?? $defaultImage) . '">' . "\n";

        return $output;
    }
}

if (!function_exists('schema_organization')) {
    /**
     * Generate Schema.org Organization markup
     */
    function schema_organization(): string
    {
        return seo_service()->schemaOrganization();
    }
}

if (!function_exists('schema_software')) {
    /**
     * Generate Schema.org SoftwareApplication markup
     */
    function schema_software(): string
    {
        return seo_service()->schemaSoftware();
    }
}

if (!function_exists('schema_breadcrumb')) {
    /**
     * Generate Schema.org BreadcrumbList markup
     */
    function schema_breadcrumb(array $items): string
    {
        return seo_service()->schemaBreadcrumb($items);
    }
}

if (!function_exists('schema_faq')) {
    /**
     * Generate Schema.org FAQPage markup
     * 
     * @param array $faqs Array of ['question' => '', 'answer' => '']
     * @return string JSON-LD script tag
     */
    function schema_faq(array $faqs): string
    {
        return seo_service()->schemaFaq($faqs);
    }
}

if (!function_exists('schema_local_business')) {
    /**
     * Generate Schema.org LocalBusiness markup
     * 
     * @return string JSON-LD script tag
     */
    function schema_local_business(): string
    {
        return seo_service()->schemaLocalBusiness();
    }
}

if (!function_exists('schema_webpage')) {
    /**
     * Generate Schema.org WebPage markup
     * 
     * @param array $data Page data
     * @return string JSON-LD script tag
     */
    function schema_webpage(array $data = []): string
    {
        return seo_service()->schemaWebPage($data);
    }
}

if (!function_exists('canonical_url')) {
    /**
     * Generate canonical URL tag
     */
    function canonical_url(?string $url = null): string
    {
        $url = $url ?? current_url();
        return '<link rel="canonical" href="' . esc($url) . '">';
    }
}

if (!function_exists('img_alt')) {
    /**
     * Generate an SEO-friendly alt tag for an image
     * 
     * @param string $filename Image filename or path
     * @param string|null $context Additional context for the alt text
     * @return string Alt text
     */
    function img_alt(string $filename, ?string $context = null): string
    {
        // Extrait le nom de base sans extension
        $basename = pathinfo($filename, PATHINFO_FILENAME);

        // Remplace les séparateurs par des espaces
        $alt = str_replace(['-', '_'], ' ', $basename);

        // Met en majuscule la première lettre
        $alt = ucfirst(strtolower($alt));

        // Ajoute le contexte si fourni
        if ($context) {
            $alt = $alt . ' - ' . $context;
        }

        return $alt;
    }
}

if (!function_exists('seo_image_name')) {
    /**
     * Generate an SEO-friendly image filename
     * 
     * @param string $title Image title or description
     * @param string $extension File extension
     * @return string SEO-friendly filename
     */
    function seo_image_name(string $title, string $extension = 'jpg'): string
    {
        // Convertit en minuscules
        $filename = strtolower($title);

        // Supprime les accents
        $filename = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $filename);

        // Remplace les espaces et caractères spéciaux par des tirets
        $filename = preg_replace('/[^a-z0-9]+/', '-', $filename);

        // Supprime les tirets multiples et aux extrémités
        $filename = trim(preg_replace('/-+/', '-', $filename), '-');

        return $filename . '.' . $extension;
    }
}

if (!function_exists('robots_meta')) {
    /**
     * Generate robots meta tag
     * 
     * @param string $directive Robots directive (index, noindex, follow, nofollow)
     * @return string Meta tag HTML
     */
    function robots_meta(string $directive = 'index, follow'): string
    {
        return '<meta name="robots" content="' . esc($directive) . '">';
    }
}

if (!function_exists('hreflang_tags')) {
    /**
     * Generate hreflang tags for international SEO
     * 
     * @param array $languages Array of ['lang' => 'fr', 'url' => 'https://...']
     * @return string HTML hreflang tags
     */
    function hreflang_tags(array $languages): string
    {
        $output = '';
        foreach ($languages as $lang) {
            $output .= '<link rel="alternate" hreflang="' . esc($lang['lang']) . '" href="' . esc($lang['url']) . '">' . "\n";
        }
        return $output;
    }
}

