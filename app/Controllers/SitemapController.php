<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Config\Seo;

/**
 * SitemapController
 * 
 * Génère dynamiquement le sitemap XML pour le SEO.
 * Inclut toutes les pages publiques avec lastmod, changefreq et priority.
 */
class SitemapController extends BaseController
{
    protected Seo $seoConfig;

    public function __construct()
    {
        $this->seoConfig = config('Seo');
    }

    /**
     * Génère et retourne le sitemap XML
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function index()
    {
        // Set XML content type
        $this->response->setContentType('application/xml');

        // Génère le sitemap
        $xml = $this->generateSitemap();

        return $this->response->setBody($xml);
    }

    /**
     * Génère le contenu XML du sitemap
     * 
     * @return string XML content
     */
    protected function generateSitemap(): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        // Pages statiques depuis la configuration
        foreach ($this->seoConfig->publicPages as $page) {
            $url = base_url($page['url']);
            $type = $page['type'];
            $changefreq = $this->seoConfig->sitemap['changefreq'][$type] ?? 'monthly';
            $priority = $this->seoConfig->sitemap['priority'][$type] ?? 0.5;

            $xml .= $this->generateUrlEntry($url, $changefreq, $priority);
        }

        // Pages dynamiques depuis la base de données
        $dynamicPages = $this->getDynamicPages();
        foreach ($dynamicPages as $page) {
            $xml .= $this->generateUrlEntry(
                base_url($page['slug']),
                'monthly',
                0.6,
                $page['updated_at'] ?? null
            );
        }

        $xml .= '</urlset>';

        return $xml;
    }

    /**
     * Génère une entrée URL pour le sitemap
     * 
     * @param string $url URL de la page
     * @param string $changefreq Fréquence de changement
     * @param float $priority Priorité (0.0 à 1.0)
     * @param string|null $lastmod Date de dernière modification
     * @return string XML entry
     */
    protected function generateUrlEntry(
        string $url,
        string $changefreq = 'monthly',
        float $priority = 0.5,
        ?string $lastmod = null
    ): string {
        $entry = "    <url>\n";
        $entry .= "        <loc>" . htmlspecialchars($url) . "</loc>\n";

        if ($lastmod) {
            $date = date('Y-m-d', strtotime($lastmod));
            $entry .= "        <lastmod>{$date}</lastmod>\n";
        } else {
            $entry .= "        <lastmod>" . date('Y-m-d') . "</lastmod>\n";
        }

        $entry .= "        <changefreq>{$changefreq}</changefreq>\n";
        $entry .= "        <priority>" . number_format($priority, 1) . "</priority>\n";
        $entry .= "    </url>\n";

        return $entry;
    }

    /**
     * Récupère les pages dynamiques depuis la base de données
     * 
     * @return array
     */
    protected function getDynamicPages(): array
    {
        try {
            $pageModel = model('PageModel');
            return $pageModel->where('is_active', true)
                ->select('slug, updated_at')
                ->findAll();
        } catch (\Exception $e) {
            log_message('warning', 'SitemapController: Could not fetch dynamic pages - ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Génère le fichier robots.txt dynamiquement
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function robots()
    {
        $this->response->setContentType('text/plain');

        $robots = "# Robots.txt for Pilom\n";
        $robots .= "# Generated dynamically\n\n";
        $robots .= "User-agent: *\n";
        $robots .= "Allow: /\n\n";

        // Disallow private areas
        $disallowPaths = [
            '/dashboard',
            '/profile',
            '/settings',
            '/account',
            '/contacts',
            '/devis',
            '/factures',
            '/reglements',
            '/depenses',
            '/products',
            '/categories',
            '/fournisseurs',
            '/treasury',
            '/documents',
            '/notifications',
            '/statistics',
            '/recurring-invoices',
            '/admin',
            '/companies',
            '/tva-rates',
        ];

        $robots .= "# Disallow private and authenticated areas\n";
        foreach ($disallowPaths as $path) {
            $robots .= "Disallow: {$path}\n";
        }

        // Disallow common non-content paths
        $robots .= "\n# Disallow system directories\n";
        $robots .= "Disallow: /app/\n";
        $robots .= "Disallow: /writable/\n";
        $robots .= "Disallow: /vendor/\n";

        // Add sitemap reference
        $robots .= "\n# Sitemap location\n";
        $robots .= "Sitemap: " . base_url('sitemap.xml') . "\n";

        return $this->response->setBody($robots);
    }

    /**
     * Notifie les moteurs de recherche du nouveau sitemap
     * Cette méthode peut être appelée par une tâche cron
     * 
     * @return array Résultats des notifications
     */
    public function notify(): array
    {
        $sitemapUrl = urlencode(base_url('sitemap.xml'));
        $results = [];

        // Google
        try {
            $googleUrl = "https://www.google.com/ping?sitemap={$sitemapUrl}";
            $response = file_get_contents($googleUrl);
            $results['google'] = ['success' => true, 'response' => 'Pinged'];
        } catch (\Exception $e) {
            $results['google'] = ['success' => false, 'error' => $e->getMessage()];
        }

        // Bing
        try {
            $bingUrl = "https://www.bing.com/ping?sitemap={$sitemapUrl}";
            $response = file_get_contents($bingUrl);
            $results['bing'] = ['success' => true, 'response' => 'Pinged'];
        } catch (\Exception $e) {
            $results['bing'] = ['success' => false, 'error' => $e->getMessage()];
        }

        log_message('info', 'Sitemap notification sent: ' . json_encode($results));

        return $results;
    }

    /**
     * Commande CLI pour générer le sitemap (utilisable via spark)
     * php spark sitemap:generate
     */
    public function generate()
    {
        $xml = $this->generateSitemap();
        $filepath = FCPATH . 'sitemap.xml';

        if (file_put_contents($filepath, $xml)) {
            return "Sitemap generated successfully at {$filepath}";
        }

        return "Failed to generate sitemap";
    }
}
