<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;

/**
 * Tests unitaires pour les fonctions helper SEO
 * 
 * Vérifie le bon fonctionnement des fonctions d'aide SEO.
 */
class SeoHelperTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        helper('seo');
    }

    /**
     * Test de la fonction seo_title
     */
    public function testSeoTitle(): void
    {
        $result = seo_title('Ma Page Test', true);

        $this->assertIsString($result);
        $this->assertStringContainsString('Ma Page Test', $result);
        $this->assertStringContainsString('Pilom', $result);
    }

    /**
     * Test de la fonction seo_description
     */
    public function testSeoDescription(): void
    {
        $shortDesc = "Description courte de test.";
        $result = seo_description($shortDesc);

        $this->assertEquals($shortDesc, $result);
    }

    /**
     * Test de la fonction seo_description avec texte long
     */
    public function testSeoDescriptionTruncation(): void
    {
        $longDesc = str_repeat("Description longue. ", 20);
        $result = seo_description($longDesc);

        $this->assertLessThanOrEqual(163, strlen($result));
    }

    /**
     * Test de la fonction img_alt
     */
    public function testImgAlt(): void
    {
        $result = img_alt('mon-image-test.jpg');

        $this->assertEquals('Mon image test', $result);
    }

    /**
     * Test de la fonction img_alt avec contexte
     */
    public function testImgAltWithContext(): void
    {
        $result = img_alt('product-photo.png', 'Produit Premium');

        $this->assertEquals('Product photo - Produit Premium', $result);
    }

    /**
     * Test de la fonction seo_image_name
     */
    public function testSeoImageName(): void
    {
        $result = seo_image_name('Mon Super Produit!');

        $this->assertEquals('mon-super-produit.jpg', $result);
    }

    /**
     * Test de la fonction seo_image_name avec extension personnalisée
     */
    public function testSeoImageNameWithExtension(): void
    {
        $result = seo_image_name('Photo de profil', 'png');

        $this->assertEquals('photo-de-profil.png', $result);
    }

    /**
     * Test de la fonction robots_meta
     */
    public function testRobotsMeta(): void
    {
        $result = robots_meta('noindex, follow');

        $this->assertStringContainsString('<meta name="robots"', $result);
        $this->assertStringContainsString('noindex, follow', $result);
    }

    /**
     * Test de la fonction robots_meta avec valeur par défaut
     */
    public function testRobotsMetaDefault(): void
    {
        $result = robots_meta();

        $this->assertStringContainsString('index, follow', $result);
    }

    /**
     * Test de la fonction canonical_url
     */
    public function testCanonicalUrl(): void
    {
        $result = canonical_url('https://pilom.fr/test');

        $this->assertStringContainsString('<link rel="canonical"', $result);
        $this->assertStringContainsString('https://pilom.fr/test', $result);
    }

    /**
     * Test de la fonction schema_faq
     */
    public function testSchemaFaq(): void
    {
        $faqs = [
            ['question' => 'Question test ?', 'answer' => 'Réponse test'],
        ];

        $result = schema_faq($faqs);

        $this->assertStringContainsString('application/ld+json', $result);
        $this->assertStringContainsString('FAQPage', $result);
        $this->assertStringContainsString('Question test', $result);
    }

    /**
     * Test de la fonction schema_organization
     */
    public function testSchemaOrganization(): void
    {
        $result = schema_organization();

        $this->assertStringContainsString('application/ld+json', $result);
        $this->assertStringContainsString('Organization', $result);
        $this->assertStringContainsString('Pilom', $result);
    }

    /**
     * Test de la fonction schema_software
     */
    public function testSchemaSoftware(): void
    {
        $result = schema_software();

        $this->assertStringContainsString('application/ld+json', $result);
        $this->assertStringContainsString('SoftwareApplication', $result);
    }

    /**
     * Test de la fonction schema_local_business
     */
    public function testSchemaLocalBusiness(): void
    {
        $result = schema_local_business();

        $this->assertStringContainsString('application/ld+json', $result);
        $this->assertStringContainsString('LocalBusiness', $result);
    }

    /**
     * Test de la fonction schema_breadcrumb
     */
    public function testSchemaBreadcrumb(): void
    {
        $items = [
            ['name' => 'Accueil', 'url' => 'https://pilom.fr/'],
            ['name' => 'Page Test', 'url' => 'https://pilom.fr/test'],
        ];

        $result = schema_breadcrumb($items);

        $this->assertStringContainsString('BreadcrumbList', $result);
        $this->assertStringContainsString('ListItem', $result);
    }

    /**
     * Test de la fonction hreflang_tags
     */
    public function testHreflangTags(): void
    {
        $languages = [
            ['lang' => 'fr', 'url' => 'https://pilom.fr/'],
            ['lang' => 'en', 'url' => 'https://pilom.fr/en/'],
        ];

        $result = hreflang_tags($languages);

        $this->assertStringContainsString('hreflang="fr"', $result);
        $this->assertStringContainsString('hreflang="en"', $result);
        $this->assertStringContainsString('rel="alternate"', $result);
    }
}
