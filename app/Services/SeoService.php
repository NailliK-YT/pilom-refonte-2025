<?php

namespace App\Services;

use Config\Seo;

/**
 * SeoService
 * 
 * Service centralisé pour la gestion du SEO.
 * Génère les meta tags, données structurées Schema.org, Open Graph et Twitter Cards.
 */
class SeoService
{
    protected Seo $config;

    public function __construct()
    {
        $this->config = config('Seo');
    }

    /**
     * Génère un meta title optimisé SEO
     * 
     * @param string $title Titre de base
     * @param bool $includeSiteName Inclure le nom du site
     * @return string Titre optimisé (50-60 caractères)
     */
    public function generateTitle(string $title, bool $includeSiteName = true): string
    {
        $separator = ' | ';
        $siteName = $this->config->siteName;
        $maxLength = $this->config->limits['title_max'];

        if ($includeSiteName) {
            $availableLength = $maxLength - strlen($separator) - strlen($siteName);

            if (strlen($title) > $availableLength) {
                $title = mb_substr($title, 0, $availableLength - 3) . '...';
            }

            return $title . $separator . $siteName;
        }

        if (strlen($title) > $maxLength) {
            return mb_substr($title, 0, $maxLength - 3) . '...';
        }

        return $title;
    }

    /**
     * Génère une meta description optimisée SEO
     * 
     * @param string $description Description de base
     * @return string Description optimisée (150-160 caractères)
     */
    public function generateDescription(string $description): string
    {
        $maxLength = $this->config->limits['description_max'];

        if (strlen($description) > $maxLength) {
            // Coupe proprement au dernier mot complet
            $description = mb_substr($description, 0, $maxLength - 3);
            $lastSpace = strrpos($description, ' ');
            if ($lastSpace !== false && $lastSpace > $maxLength - 20) {
                $description = mb_substr($description, 0, $lastSpace);
            }
            return $description . '...';
        }

        return $description;
    }

    /**
     * Génère toutes les meta tags SEO pour une page
     * 
     * @param array $data Données SEO de la page
     * @return string HTML des meta tags
     */
    public function generateMetaTags(array $data = []): string
    {
        $title = $data['title'] ?? $this->config->siteTitle;
        $description = $data['description'] ?? $this->config->defaultDescription;
        $keywords = $data['keywords'] ?? $this->config->defaultKeywords;
        $robots = $data['robots'] ?? $this->config->robotsDirectives['public'];
        $canonical = $data['canonical'] ?? current_url();

        $output = '';

        // Meta description
        $output .= '<meta name="description" content="' . esc($this->generateDescription($description)) . '">' . "\n";

        // Meta keywords
        if (!empty($keywords)) {
            $output .= '<meta name="keywords" content="' . esc($keywords) . '">' . "\n";
        }

        // Robots
        $output .= '<meta name="robots" content="' . esc($robots) . '">' . "\n";

        // Canonical URL
        $output .= '<link rel="canonical" href="' . esc($canonical) . '">' . "\n";

        return $output;
    }

    /**
     * Génère les balises Open Graph
     * 
     * @param array $data Données OG
     * @return string HTML des balises OG
     */
    public function generateOpenGraph(array $data = []): string
    {
        $title = $data['og_title'] ?? $data['title'] ?? $this->config->siteTitle;
        $description = $data['og_description'] ?? $data['description'] ?? $this->config->defaultDescription;
        $image = $data['og_image'] ?? base_url($this->config->defaultOgImage);
        $type = $data['og_type'] ?? $this->config->defaultOgType;
        $url = $data['url'] ?? current_url();

        $output = '';
        $output .= '<meta property="og:type" content="' . esc($type) . '">' . "\n";
        $output .= '<meta property="og:title" content="' . esc($this->generateTitle($title, false)) . '">' . "\n";
        $output .= '<meta property="og:description" content="' . esc($this->generateDescription($description)) . '">' . "\n";
        $output .= '<meta property="og:url" content="' . esc($url) . '">' . "\n";
        $output .= '<meta property="og:image" content="' . esc($image) . '">' . "\n";
        $output .= '<meta property="og:site_name" content="' . esc($this->config->siteName) . '">' . "\n";
        $output .= '<meta property="og:locale" content="fr_FR">' . "\n";

        return $output;
    }

    /**
     * Génère les balises Twitter Card
     * 
     * @param array $data Données Twitter
     * @return string HTML des balises Twitter
     */
    public function generateTwitterCards(array $data = []): string
    {
        $title = $data['twitter_title'] ?? $data['title'] ?? $this->config->siteTitle;
        $description = $data['twitter_description'] ?? $data['description'] ?? $this->config->defaultDescription;
        $image = $data['twitter_image'] ?? $data['og_image'] ?? base_url($this->config->defaultOgImage);
        $cardType = $data['twitter_card'] ?? $this->config->twitterCardType;

        $output = '';
        $output .= '<meta name="twitter:card" content="' . esc($cardType) . '">' . "\n";
        $output .= '<meta name="twitter:site" content="@' . esc($this->config->twitterSite) . '">' . "\n";
        $output .= '<meta name="twitter:title" content="' . esc($this->generateTitle($title, false)) . '">' . "\n";
        $output .= '<meta name="twitter:description" content="' . esc($this->generateDescription($description)) . '">' . "\n";
        $output .= '<meta name="twitter:image" content="' . esc($image) . '">' . "\n";

        return $output;
    }

    /**
     * Génère toutes les balises SEO (meta + OG + Twitter)
     * 
     * @param array $data Données SEO
     * @return string HTML complet des balises SEO
     */
    public function generateAllTags(array $data = []): string
    {
        $output = "    <!-- SEO Meta Tags -->\n";
        $output .= $this->generateMetaTags($data);
        $output .= "\n    <!-- Open Graph -->\n";
        $output .= $this->generateOpenGraph($data);
        $output .= "\n    <!-- Twitter Cards -->\n";
        $output .= $this->generateTwitterCards($data);

        return $output;
    }

    /**
     * Génère le Schema.org Organization
     * 
     * @return string JSON-LD script tag
     */
    public function schemaOrganization(): string
    {
        $schema = $this->config->organization;
        $schema['url'] = base_url();
        $schema['logo'] = base_url('images/logo.png');

        return $this->wrapSchema($schema);
    }

    /**
     * Génère le Schema.org SoftwareApplication
     * 
     * @return string JSON-LD script tag
     */
    public function schemaSoftware(): string
    {
        $schema = $this->config->software;
        $schema['url'] = base_url();

        return $this->wrapSchema($schema);
    }

    /**
     * Génère le Schema.org LocalBusiness
     * 
     * @return string JSON-LD script tag
     */
    public function schemaLocalBusiness(): string
    {
        $schema = $this->config->localBusiness;
        $schema['url'] = base_url();

        return $this->wrapSchema($schema);
    }

    /**
     * Génère le Schema.org FAQPage
     * 
     * @param array $faqs Tableau de questions/réponses ['question' => '', 'answer' => '']
     * @return string JSON-LD script tag
     */
    public function schemaFaq(array $faqs): string
    {
        $mainEntity = [];

        foreach ($faqs as $faq) {
            $mainEntity[] = [
                '@type' => 'Question',
                'name' => $faq['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $faq['answer'],
                ],
            ];
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $mainEntity,
        ];

        return $this->wrapSchema($schema);
    }

    /**
     * Génère le Schema.org BreadcrumbList
     * 
     * @param array $items Tableau de ['name' => '', 'url' => '']
     * @return string JSON-LD script tag
     */
    public function schemaBreadcrumb(array $items): string
    {
        $itemListElement = [];

        foreach ($items as $index => $item) {
            $itemListElement[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $item['name'],
                'item' => $item['url'],
            ];
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $itemListElement,
        ];

        return $this->wrapSchema($schema);
    }

    /**
     * Génère le Schema.org WebPage
     * 
     * @param array $data Données de la page
     * @return string JSON-LD script tag
     */
    public function schemaWebPage(array $data = []): string
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => $data['title'] ?? $this->config->siteName,
            'description' => $data['description'] ?? $this->config->defaultDescription,
            'url' => $data['url'] ?? current_url(),
            'isPartOf' => [
                '@type' => 'WebSite',
                'name' => $this->config->siteName,
                'url' => base_url(),
            ],
        ];

        if (!empty($data['dateModified'])) {
            $schema['dateModified'] = $data['dateModified'];
        }

        return $this->wrapSchema($schema);
    }

    /**
     * Enveloppe un schema dans une balise script JSON-LD
     * 
     * @param array $schema Données du schema
     * @return string HTML script tag
     */
    protected function wrapSchema(array $schema): string
    {
        return '<script type="application/ld+json">' .
            json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) .
            '</script>';
    }

    /**
     * Valide les données SEO
     * 
     * @param array $data Données à valider
     * @return array Erreurs de validation (vide si tout est OK)
     */
    public function validate(array $data): array
    {
        $errors = [];
        $limits = $this->config->limits;

        if (!empty($data['title'])) {
            $titleLength = mb_strlen($data['title']);
            if ($titleLength < $limits['title_min']) {
                $errors['title'] = "Le titre est trop court ({$titleLength} caractères, minimum {$limits['title_min']})";
            } elseif ($titleLength > $limits['title_max']) {
                $errors['title'] = "Le titre est trop long ({$titleLength} caractères, maximum {$limits['title_max']})";
            }
        }

        if (!empty($data['description'])) {
            $descLength = mb_strlen($data['description']);
            if ($descLength < $limits['description_min']) {
                $errors['description'] = "La description est trop courte ({$descLength} caractères, minimum {$limits['description_min']})";
            } elseif ($descLength > $limits['description_max']) {
                $errors['description'] = "La description est trop longue ({$descLength} caractères, maximum {$limits['description_max']})";
            }
        }

        return $errors;
    }

    /**
     * Obtient la configuration SEO
     * 
     * @return Seo
     */
    public function getConfig(): Seo
    {
        return $this->config;
    }
}
