<?php

/**
 * SEO Helper Functions
 * 
 * Helper functions for SEO optimization
 */

if (!function_exists('seo_meta')) {
    /**
     * Generate meta tags for SEO
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
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => 'Pilom',
            'description' => 'Logiciel de facturation et gestion commerciale pour indépendants et PME',
            'url' => base_url(),
            'logo' => base_url('images/logo.png'),
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'contactType' => 'customer service',
                'email' => 'contact@pilom.fr'
            ]
        ];

        return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
    }
}

if (!function_exists('schema_software')) {
    /**
     * Generate Schema.org SoftwareApplication markup
     */
    function schema_software(): string
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'SoftwareApplication',
            'name' => 'Pilom',
            'applicationCategory' => 'BusinessApplication',
            'operatingSystem' => 'Web',
            'description' => 'Solution complète de facturation, devis et gestion commerciale',
            'offers' => [
                '@type' => 'Offer',
                'price' => '0',
                'priceCurrency' => 'EUR'
            ]
        ];

        return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
    }
}

if (!function_exists('schema_breadcrumb')) {
    /**
     * Generate Schema.org BreadcrumbList markup
     */
    function schema_breadcrumb(array $items): string
    {
        $listItems = [];
        foreach ($items as $i => $item) {
            $listItems[] = [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => $item['name'],
                'item' => $item['url']
            ];
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $listItems
        ];

        return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
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
