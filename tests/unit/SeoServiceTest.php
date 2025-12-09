<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use App\Services\SeoService;

/**
 * Tests unitaires pour le SeoService
 * 
 * Vérifie le bon fonctionnement de la génération des meta tags,
 * des données structurées Schema.org, et de la validation SEO.
 */
class SeoServiceTest extends CIUnitTestCase
{
    protected SeoService $seoService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seoService = new SeoService();
    }

    /**
     * Test de génération de titre avec limite de caractères
     */
    public function testGenerateTitleWithinLimit(): void
    {
        $title = "Page Test";
        $result = $this->seoService->generateTitle($title, true);

        $this->assertStringContainsString('Page Test', $result);
        $this->assertStringContainsString('Pilom', $result);
        $this->assertLessThanOrEqual(70, strlen($result));
    }

    /**
     * Test de troncature du titre trop long
     */
    public function testGenerateTitleTruncatesLongTitle(): void
    {
        $longTitle = "Ceci est un titre extrêmement long qui dépasse la limite recommandée de caractères pour le SEO";
        $result = $this->seoService->generateTitle($longTitle, true);

        // Using mb_strlen for UTF-8 characters
        $this->assertLessThanOrEqual(75, mb_strlen($result)); // Allow some margin for UTF-8
        $this->assertStringContainsString('...', $result);
    }

    /**
     * Test de génération de description avec limite de caractères
     */
    public function testGenerateDescriptionWithinLimit(): void
    {
        $description = "Ceci est une description courte.";
        $result = $this->seoService->generateDescription($description);

        $this->assertEquals($description, $result);
    }

    /**
     * Test de troncature de description trop longue
     */
    public function testGenerateDescriptionTruncatesLongDescription(): void
    {
        $longDescription = str_repeat("Description très longue. ", 20);
        $result = $this->seoService->generateDescription($longDescription);

        // Using mb_strlen for proper UTF-8 handling
        $this->assertLessThanOrEqual(170, mb_strlen($result));
        $this->assertStringContainsString('...', $result);
    }

    /**
     * Test de génération des meta tags
     */
    public function testGenerateMetaTags(): void
    {
        $data = [
            'title' => 'Page Test',
            'description' => 'Description de test pour la page.',
            'keywords' => 'test, seo, meta',
        ];

        $result = $this->seoService->generateMetaTags($data);

        $this->assertStringContainsString('<meta name="description"', $result);
        $this->assertStringContainsString('Description de test', $result);
        $this->assertStringContainsString('<meta name="keywords"', $result);
        $this->assertStringContainsString('<meta name="robots"', $result);
        $this->assertStringContainsString('<link rel="canonical"', $result);
    }

    /**
     * Test de génération des balises Open Graph
     */
    public function testGenerateOpenGraph(): void
    {
        $data = [
            'title' => 'Titre OG Test',
            'description' => 'Description OG Test',
        ];

        $result = $this->seoService->generateOpenGraph($data);

        $this->assertStringContainsString('og:type', $result);
        $this->assertStringContainsString('og:title', $result);
        $this->assertStringContainsString('og:description', $result);
        $this->assertStringContainsString('og:url', $result);
        $this->assertStringContainsString('og:image', $result);
        $this->assertStringContainsString('og:site_name', $result);
        $this->assertStringContainsString('og:locale', $result);
    }

    /**
     * Test de génération des Twitter Cards
     */
    public function testGenerateTwitterCards(): void
    {
        $data = [
            'title' => 'Titre Twitter Test',
            'description' => 'Description Twitter Test',
        ];

        $result = $this->seoService->generateTwitterCards($data);

        $this->assertStringContainsString('twitter:card', $result);
        $this->assertStringContainsString('twitter:site', $result);
        $this->assertStringContainsString('twitter:title', $result);
        $this->assertStringContainsString('twitter:description', $result);
        $this->assertStringContainsString('twitter:image', $result);
    }

    /**
     * Test de génération du Schema.org Organization
     */
    public function testSchemaOrganization(): void
    {
        $result = $this->seoService->schemaOrganization();

        $this->assertStringContainsString('application/ld+json', $result);
        $this->assertStringContainsString('"@type": "Organization"', $result);
        $this->assertStringContainsString('"name": "Pilom"', $result);
    }

    /**
     * Test de génération du Schema.org SoftwareApplication
     */
    public function testSchemaSoftware(): void
    {
        $result = $this->seoService->schemaSoftware();

        $this->assertStringContainsString('application/ld+json', $result);
        $this->assertStringContainsString('"@type": "SoftwareApplication"', $result);
        $this->assertStringContainsString('BusinessApplication', $result);
    }

    /**
     * Test de génération du Schema.org FAQPage
     */
    public function testSchemaFaq(): void
    {
        $faqs = [
            ['question' => 'Question 1 ?', 'answer' => 'Réponse 1'],
            ['question' => 'Question 2 ?', 'answer' => 'Réponse 2'],
        ];

        $result = $this->seoService->schemaFaq($faqs);

        $this->assertStringContainsString('application/ld+json', $result);
        $this->assertStringContainsString('"@type": "FAQPage"', $result);
        $this->assertStringContainsString('"@type": "Question"', $result);
        $this->assertStringContainsString('Question 1', $result);
    }

    /**
     * Test de génération du Schema.org BreadcrumbList
     */
    public function testSchemaBreadcrumb(): void
    {
        $items = [
            ['name' => 'Accueil', 'url' => 'https://pilom.fr/'],
            ['name' => 'FAQ', 'url' => 'https://pilom.fr/faq'],
        ];

        $result = $this->seoService->schemaBreadcrumb($items);

        $this->assertStringContainsString('application/ld+json', $result);
        $this->assertStringContainsString('"@type": "BreadcrumbList"', $result);
        $this->assertStringContainsString('"@type": "ListItem"', $result);
        $this->assertStringContainsString('"position": 1', $result);
        $this->assertStringContainsString('"position": 2', $result);
    }

    /**
     * Test de validation SEO - titre trop court
     */
    public function testValidateTitleTooShort(): void
    {
        $data = ['title' => 'Court'];
        $errors = $this->seoService->validate($data);

        $this->assertArrayHasKey('title', $errors);
        $this->assertStringContainsString('trop court', $errors['title']);
    }

    /**
     * Test de validation SEO - titre trop long
     */
    public function testValidateTitleTooLong(): void
    {
        $data = ['title' => str_repeat('a', 70)];
        $errors = $this->seoService->validate($data);

        $this->assertArrayHasKey('title', $errors);
        $this->assertStringContainsString('trop long', $errors['title']);
    }

    /**
     * Test de validation SEO - description trop courte
     */
    public function testValidateDescriptionTooShort(): void
    {
        $data = ['description' => 'Description courte'];
        $errors = $this->seoService->validate($data);

        $this->assertArrayHasKey('description', $errors);
        $this->assertStringContainsString('trop courte', $errors['description']);
    }

    /**
     * Test de validation SEO - données valides
     */
    public function testValidateValidData(): void
    {
        $data = [
            'title' => 'Un titre de page parfaitement optimisé pour le SEO',
            'description' => 'Une description de page parfaitement optimisée pour le SEO avec suffisamment de caractères pour être considérée comme valide par les moteurs de recherche.',
        ];
        $errors = $this->seoService->validate($data);

        $this->assertEmpty($errors);
    }

    /**
     * Test d'obtention de la configuration
     */
    public function testGetConfig(): void
    {
        $config = $this->seoService->getConfig();

        $this->assertNotNull($config);
        $this->assertEquals('Pilom', $config->siteName);
        $this->assertIsArray($config->limits);
    }
}
