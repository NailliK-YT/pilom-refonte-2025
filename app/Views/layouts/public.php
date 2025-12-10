<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- SEO Meta Tags -->
    <title><?= esc($seo['title'] ?? $title ?? 'Pilom - Gestion pour Artisans et Indépendants') ?></title>
    <meta name="description"
        content="<?= esc($seo['description'] ?? $description ?? 'Pilom - La solution de gestion tout-en-un pour les artisans, commerçants et indépendants. Facturation, devis, comptabilité simplifiée.') ?>">
    <?php if (!empty($seo['keywords'] ?? $keywords ?? '')): ?>
        <meta name="keywords" content="<?= esc($seo['keywords'] ?? $keywords) ?>">
    <?php endif; ?>
    <meta name="robots" content="<?= esc($seo['robots'] ?? 'index, follow') ?>">
    <link rel="canonical" href="<?= esc($seo['canonical'] ?? current_url()) ?>">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="<?= esc($seo['og_type'] ?? 'website') ?>">
    <meta property="og:url" content="<?= esc($seo['canonical'] ?? current_url()) ?>">
    <meta property="og:title" content="<?= esc($seo['og_title'] ?? $seo['title'] ?? $title ?? 'Pilom') ?>">
    <meta property="og:description"
        content="<?= esc($seo['og_description'] ?? $seo['description'] ?? $description ?? 'Pilom - Gestion simplifiée pour entrepreneurs') ?>">
    <meta property="og:image" content="<?= esc($seo['og_image'] ?? base_url('images/og-default.jpg')) ?>">
    <meta property="og:site_name" content="Pilom">
    <meta property="og:locale" content="fr_FR">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@pilom_fr">
    <meta name="twitter:title" content="<?= esc($seo['twitter_title'] ?? $seo['title'] ?? $title ?? 'Pilom') ?>">
    <meta name="twitter:description"
        content="<?= esc($seo['twitter_description'] ?? $seo['description'] ?? $description ?? 'Pilom - Gestion simplifiée pour entrepreneurs') ?>">
    <meta name="twitter:image"
        content="<?= esc($seo['twitter_image'] ?? $seo['og_image'] ?? base_url('images/og-default.jpg')) ?>">

    <!-- Performance: Preconnect for external resources -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Stylesheets with versioning for cache busting -->
    <link rel="stylesheet" href="<?= asset_url('css/style.css') ?>">
    <link rel="stylesheet" href="<?= asset_url('css/pages.css') ?>">
    <link rel="stylesheet" href="<?= asset_url('css/blog.css') ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Additional page-specific styles -->
    <?= $this->renderSection('styles') ?>

    <!-- Schema.org Structured Data -->
    <?= $schema ?? '' ?>
</head>

<body>
    <nav class="navbar">
        <div class="logo">
            <div style="display: flex; align-items: center; gap: 10px;">
                <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M16 2L2 9V23L16 30L30 23V9L16 2Z" fill="#4E51C0" />
                    <circle cx="16" cy="16" r="6" fill="white" />
                </svg>
                Pilom
            </div>
        </div>
        <div class="nav-links">
            <div class="dropdown">
                <a href="#" class="dropdown-trigger">Fonctionnalités</a>
                <div class="dropdown-content">
                    <a href="<?= base_url('fonctionnalites/facturation') ?>">Facturation</a>
                    <a href="<?= base_url('fonctionnalites/devis') ?>">Devis</a>
                    <a href="<?= base_url('fonctionnalites/contacts') ?>">Contacts</a>
                    <a href="<?= base_url('fonctionnalites/depenses') ?>">Dépenses</a>
                    <a href="<?= base_url('fonctionnalites/tresorerie') ?>">Trésorerie</a>
                </div>
            </div>

            <div class="dropdown">
                <a href="#" class="dropdown-trigger">Pour qui ?</a>
                <div class="dropdown-content">
                    <a href="<?= base_url('pour/artisan') ?>">Artisan</a>
                    <a href="<?= base_url('pour/consultant') ?>">Consultant</a>
                    <a href="<?= base_url('pour/freelance') ?>">Freelance</a>
                    <a href="<?= base_url('pour/pme') ?>">PME</a>
                    <a href="<?= base_url('pour/auto-entrepreneur') ?>">Auto-entrepreneur</a>
                    <a href="<?= base_url('pour/profession-liberale') ?>">Profession libérale</a>
                </div>
            </div>

            <a href="<?= base_url('/') ?>#pricing">Tarifs</a>
            <a href="<?= base_url('about') ?>">À propos</a>
            <a href="<?= base_url('faq') ?>">FAQ</a>
            <a href="<?= base_url('blog') ?>">Blog</a>

            <div class="dropdown">
                <a href="#" class="dropdown-trigger">Légal</a>
                <div class="dropdown-content">
                    <a href="<?= base_url('mentions-legales') ?>">Mentions légales</a>
                    <a href="<?= base_url('cgu') ?>">CGU/CGV</a>
                    <a href="<?= base_url('confidentialite') ?>">Confidentialité</a>
                </div>
            </div>

            <a href="<?= base_url('contact') ?>">Contact</a>
        </div>
        <div class="nav-auth">
            <a href="<?= base_url('login') ?>" class="btn btn-outline">Connexion</a>
            <a href="<?= base_url('register') ?>" class="btn btn-primary">Essai Gratuit</a>
        </div>
    </nav>

    <main class="page-content">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <?= $this->renderSection('content') ?>
    </main>

    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h4>Pilom</h4>
                <p>La solution de gestion pour les entrepreneurs ambitieux.</p>
            </div>
            <div class="footer-section">
                <h4>Produit</h4>
                <ul>
                    <li><a href="<?= base_url('/') ?>#features">Fonctionnalités</a></li>
                    <li><a href="<?= base_url('/') ?>#pricing">Tarifs</a></li>
                    <li><a href="<?= base_url('about') ?>">À propos</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Support</h4>
                <ul>
                    <li><a href="<?= base_url('contact') ?>">Contact</a></li>
                    <li><a href="<?= base_url('faq') ?>">FAQ</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Légal</h4>
                <ul>
                    <li><a href="<?= base_url('mentions-legales') ?>">Mentions légales</a></li>
                    <li><a href="<?= base_url('confidentialite') ?>">Confidentialité</a></li>
                    <li><a href="<?= base_url('cgu') ?>">CGU</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> Pilom. Tous droits réservés.</p>
        </div>
    </footer>
</body>

</html>