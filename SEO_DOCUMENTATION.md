# Documentation SEO - Pilom

## Vue d'ensemble

Système SEO complet pour pilom.fr incluant meta tags dynamiques, Schema.org, sitemap XML, et redirections 301.

## Fichiers créés

| Fichier | Description |
|---------|-------------|
| `Config/Seo.php` | Configuration centralisée |
| `Services/SeoService.php` | Service SEO principal |
| `Models/SeoMetaModel.php` | Métadonnées par entité |
| `Models/UrlRedirectModel.php` | Redirections 301 |
| `Controllers/SitemapController.php` | Sitemap XML dynamique |
| `Filters/RedirectFilter.php` | Gestion des redirections |

---

## Utilisation

### Meta Tags dans les contrôleurs

```php
// Dans un contrôleur
$data = [
    'seo' => [
        'title' => 'Titre de la page | Pilom',
        'description' => 'Description optimisée de 150-160 caractères...',
        'keywords' => 'mot1, mot2, mot3',
    ]
];
return view('pages/mapage', $data);
```

### Schema.org dans les vues

```php
// Charger le helper
helper('seo');

// FAQ
echo schema_faq($faqs);

// Organisation
echo schema_organization();

// LocalBusiness (contact)
echo schema_local_business();

// Breadcrumb
echo schema_breadcrumb([
    ['name' => 'Accueil', 'url' => base_url()],
    ['name' => 'FAQ', 'url' => base_url('faq')],
]);
```

### Helpers utiles

```php
// Titre optimisé (50-60 chars)
seo_title('Mon titre');

// Description optimisée (150-160 chars)
seo_description('Ma description...');

// Alt tag automatique pour images
img_alt('mon-image.jpg'); // "Mon image"

// Nom de fichier SEO-friendly
seo_image_name('Mon Produit'); // "mon-produit.jpg"
```

---

## Configuration

Modifier `app/Config/Seo.php` pour :
- Nom du site et descriptions par défaut
- Limites de caractères
- Données Schema.org Organization
- Pages du sitemap

---

## Maintenance

### Sitemap
- Généré dynamiquement à `/sitemap.xml`
- Mettre à jour les pages dans `Config/Seo.php::$publicPages`

### Redirections 301
1. Ajouter via la base de données table `url_redirects`
2. Le filtre `redirect` gère automatiquement

### Migrations
```bash
php spark migrate
```

---

## Tests

```bash
# Tests unitaires SEO
./vendor/bin/phpunit tests/unit/SeoServiceTest.php
./vendor/bin/phpunit tests/unit/SeoHelperTest.php
```

## Validation

1. **Sitemap**: https://www.xml-sitemaps.com/validate-xml-sitemap.html
2. **Schema.org**: https://validator.schema.org/
3. **Open Graph**: https://developers.facebook.com/tools/debug/
4. **Lighthouse**: Chrome DevTools > Lighthouse > SEO (cible: >95)
