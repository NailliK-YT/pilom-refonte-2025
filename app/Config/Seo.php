<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * SEO Configuration
 * 
 * Configuration centrale pour toutes les fonctionnalités SEO du site Pilom.
 */
class Seo extends BaseConfig
{
    /**
     * Nom du site
     */
    public string $siteName = 'Pilom';

    /**
     * Titre complet du site pour les balises title
     */
    public string $siteTitle = 'Pilom - Logiciel de Gestion pour Artisans et Indépendants';

    /**
     * Description par défaut du site
     */
    public string $defaultDescription = 'Pilom est la solution tout-en-un pour les artisans, commerçants et indépendants. Facturation, devis, comptabilité simplifiée et gestion commerciale.';

    /**
     * Mots-clés par défaut
     */
    public string $defaultKeywords = 'facturation, devis, gestion commerciale, artisan, indépendant, PME, comptabilité, logiciel gestion';

    /**
     * URL de base du site (production)
     */
    public string $siteUrl = 'https://pilom.fr';

    /**
     * Limites de caractères pour le SEO
     */
    public array $limits = [
        'title_min' => 50,
        'title_max' => 60,
        'description_min' => 150,
        'description_max' => 160,
        'keywords_max' => 255,
    ];

    /**
     * Image Open Graph par défaut
     */
    public string $defaultOgImage = '/images/og-default.jpg';

    /**
     * Type de contenu Open Graph par défaut
     */
    public string $defaultOgType = 'website';

    /**
     * Identifiant Twitter du site (sans @)
     */
    public string $twitterSite = 'pilom_fr';

    /**
     * Type de Twitter Card par défaut
     */
    public string $twitterCardType = 'summary_large_image';

    /**
     * Données Schema.org Organization
     */
    public array $organization = [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => 'Pilom',
        'description' => 'Logiciel de gestion tout-en-un pour artisans, commerçants et indépendants',
        'url' => 'https://pilom.fr',
        'logo' => 'https://pilom.fr/images/logo.png',
        'email' => 'contact@pilom.fr',
        'sameAs' => [
            'https://www.linkedin.com/company/pilom',
            'https://twitter.com/pilom_fr',
        ],
        'contactPoint' => [
            '@type' => 'ContactPoint',
            'contactType' => 'customer service',
            'email' => 'support@pilom.fr',
            'availableLanguage' => ['French'],
        ],
    ];

    /**
     * Données Schema.org SoftwareApplication
     */
    public array $software = [
        '@context' => 'https://schema.org',
        '@type' => 'SoftwareApplication',
        'name' => 'Pilom',
        'applicationCategory' => 'BusinessApplication',
        'operatingSystem' => 'Web',
        'offers' => [
            '@type' => 'Offer',
            'price' => '0',
            'priceCurrency' => 'EUR',
            'description' => 'Essai gratuit',
        ],
        'aggregateRating' => [
            '@type' => 'AggregateRating',
            'ratingValue' => '4.8',
            'ratingCount' => '150',
        ],
    ];

    /**
     * Données Schema.org LocalBusiness (pour page contact)
     */
    public array $localBusiness = [
        '@context' => 'https://schema.org',
        '@type' => 'LocalBusiness',
        'name' => 'Pilom',
        'description' => 'Éditeur de logiciel de gestion pour professionnels',
        'url' => 'https://pilom.fr',
        'email' => 'contact@pilom.fr',
        'address' => [
            '@type' => 'PostalAddress',
            'addressCountry' => 'FR',
        ],
    ];

    /**
     * Configuration du sitemap
     */
    public array $sitemap = [
        'changefreq' => [
            'home' => 'weekly',
            'features' => 'monthly',
            'about' => 'monthly',
            'contact' => 'monthly',
            'faq' => 'monthly',
            'legal' => 'yearly',
            'auth' => 'monthly',
        ],
        'priority' => [
            'home' => 1.0,
            'features' => 0.8,
            'about' => 0.7,
            'contact' => 0.7,
            'faq' => 0.6,
            'legal' => 0.3,
            'auth' => 0.5,
        ],
    ];

    /**
     * Robots meta par type de page
     */
    public array $robotsDirectives = [
        'public' => 'index, follow',
        'private' => 'noindex, nofollow',
        'legal' => 'index, nofollow',
    ];

    /**
     * Pages publiques pour le sitemap (routes statiques)
     */
    public array $publicPages = [
        ['url' => '/', 'type' => 'home', 'title' => 'Accueil'],
        ['url' => '/about', 'type' => 'about', 'title' => 'À propos'],
        ['url' => '/contact', 'type' => 'contact', 'title' => 'Contact'],
        ['url' => '/faq', 'type' => 'faq', 'title' => 'FAQ'],
        ['url' => '/mentions-legales', 'type' => 'legal', 'title' => 'Mentions légales'],
        ['url' => '/cgu', 'type' => 'legal', 'title' => 'CGU'],
        ['url' => '/confidentialite', 'type' => 'legal', 'title' => 'Confidentialité'],
        ['url' => '/login', 'type' => 'auth', 'title' => 'Connexion'],
        ['url' => '/register', 'type' => 'auth', 'title' => 'Inscription'],
        ['url' => '/fonctionnalites/facturation', 'type' => 'features', 'title' => 'Facturation'],
        ['url' => '/fonctionnalites/devis', 'type' => 'features', 'title' => 'Devis'],
        ['url' => '/fonctionnalites/contacts', 'type' => 'features', 'title' => 'Contacts'],
        ['url' => '/fonctionnalites/depenses', 'type' => 'features', 'title' => 'Dépenses'],
        ['url' => '/fonctionnalites/tresorerie', 'type' => 'features', 'title' => 'Trésorerie'],
        ['url' => '/pour/artisan', 'type' => 'features', 'title' => 'Pour les Artisans'],
        ['url' => '/pour/consultant', 'type' => 'features', 'title' => 'Pour les Consultants'],
        ['url' => '/pour/freelance', 'type' => 'features', 'title' => 'Pour les Freelances'],
        ['url' => '/pour/pme', 'type' => 'features', 'title' => 'Pour les PME'],
        ['url' => '/pour/auto-entrepreneur', 'type' => 'features', 'title' => 'Pour les Auto-entrepreneurs'],
        ['url' => '/pour/profession-liberale', 'type' => 'features', 'title' => 'Pour les Professions libérales'],
    ];
}
