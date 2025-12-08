<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title', true) ?> - Pilom</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/dashboard.css') ?>">
    <?= $this->renderSection('styles') ?>
</head>

<body class="dashboard-body">
    <?php
    // Helper function to check active state
    $currentUri = service('request')->getUri();
    $currentSegment1 = $currentUri->getSegment(1);
    $currentPath = $currentUri->getPath();

    function isActive($segment, $currentSegment1, $currentPath = null, $exactPath = null)
    {
        if ($exactPath !== null) {
            return strpos($currentPath, $exactPath) !== false;
        }
        return $currentSegment1 === $segment;
    }
    ?>

    <!-- Left Sidebar Navigation -->
    <aside class="sidebar" id="sidebar">
        <!-- Logo Section -->
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <div class="logo-icon">
                    <svg width="28" height="28" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M16 2L2 9V23L16 30L30 23V9L16 2Z" fill="currentColor" />
                        <circle cx="16" cy="16" r="5" fill="white" />
                    </svg>
                </div>
                <span class="logo-text">Pilom</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <!-- Main Navigation -->
            <div class="sidebar-section">
                <div class="sidebar-section-title">Principal</div>

                <a href="<?= base_url('dashboard') ?>"
                    class="sidebar-link <?= isActive('dashboard', $currentSegment1) ? 'active' : '' ?>">
                    <svg class="sidebar-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path
                            d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                    </svg>
                    <span>Tableau de bord</span>
                </a>

                <a href="<?= base_url('treasury') ?>"
                    class="sidebar-link <?= isActive('treasury', $currentSegment1) ? 'active' : '' ?>">
                    <svg class="sidebar-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z" />
                        <path fill-rule="evenodd"
                            d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"
                            clip-rule="evenodd" />
                    </svg>
                    <span>Trésorerie</span>
                </a>
            </div>

            <!-- Commercial Section -->
            <div class="sidebar-section">
                <div class="sidebar-section-title">Commercial</div>

                <a href="<?= base_url('contacts') ?>"
                    class="sidebar-link <?= isActive('contacts', $currentSegment1) ? 'active' : '' ?>">
                    <svg class="sidebar-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path
                            d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                    </svg>
                    <span>Contacts</span>
                </a>

                <a href="<?= base_url('devis') ?>"
                    class="sidebar-link <?= isActive('devis', $currentSegment1) ? 'active' : '' ?>">
                    <svg class="sidebar-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                            clip-rule="evenodd" />
                    </svg>
                    <span>Devis</span>
                </a>

                <a href="<?= base_url('factures') ?>"
                    class="sidebar-link <?= isActive('factures', $currentSegment1) ? 'active' : '' ?>">
                    <svg class="sidebar-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"
                            clip-rule="evenodd" />
                    </svg>
                    <span>Factures</span>
                </a>

                <a href="<?= base_url('reglements') ?>"
                    class="sidebar-link <?= isActive('reglements', $currentSegment1) ? 'active' : '' ?>">
                    <svg class="sidebar-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z" />
                        <path fill-rule="evenodd"
                            d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"
                            clip-rule="evenodd" />
                    </svg>
                    <span>Règlements</span>
                </a>

                <a href="<?= base_url('recurring-invoices') ?>"
                    class="sidebar-link <?= isActive('recurring-invoices', $currentSegment1) ? 'active' : '' ?>">
                    <svg class="sidebar-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
                            clip-rule="evenodd" />
                    </svg>
                    <span>Factures Récurrentes</span>
                </a>
            </div>

            <!-- Catalog Section -->
            <div class="sidebar-section">
                <div class="sidebar-section-title">Catalogue</div>

                <a href="<?= base_url('products') ?>"
                    class="sidebar-link <?= isActive('products', $currentSegment1) ? 'active' : '' ?>">
                    <svg class="sidebar-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z"
                            clip-rule="evenodd" />
                    </svg>
                    <span>Produits</span>
                </a>

                <a href="<?= base_url('categories') ?>"
                    class="sidebar-link <?= isActive('categories', $currentSegment1) ? 'active' : '' ?>">
                    <svg class="sidebar-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" />
                    </svg>
                    <span>Catégories</span>
                </a>

                <a href="<?= base_url('tva-rates') ?>"
                    class="sidebar-link <?= isActive('tva-rates', $currentSegment1) ? 'active' : '' ?>">
                    <svg class="sidebar-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path
                            d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z"
                            clip-rule="evenodd" />
                    </svg>
                    <span>Taux de TVA</span>
                </a>
            </div>

            <!-- Expenses Section -->
            <div class="sidebar-section">
                <div class="sidebar-section-title">Dépenses</div>

                <a href="<?= base_url('depenses') ?>"
                    class="sidebar-link <?= isActive('depenses', $currentSegment1) ? 'active' : '' ?>">
                    <svg class="sidebar-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M5 2a2 2 0 00-2 2v14l3.5-2 3.5 2 3.5-2 3.5 2V4a2 2 0 00-2-2H5zm2.5 3a1.5 1.5 0 100 3 1.5 1.5 0 000-3zm6.207.293a1 1 0 00-1.414 0l-6 6a1 1 0 101.414 1.414l6-6a1 1 0 000-1.414zM12.5 10a1.5 1.5 0 100 3 1.5 1.5 0 000-3z"
                            clip-rule="evenodd" />
                    </svg>
                    <span>Mes dépenses</span>
                </a>

                <a href="<?= base_url('fournisseurs') ?>"
                    class="sidebar-link <?= isActive('fournisseurs', $currentSegment1) ? 'active' : '' ?>">
                    <svg class="sidebar-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                        <path
                            d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z" />
                    </svg>
                    <span>Fournisseurs</span>
                </a>
            </div>

            <!-- Documents Section -->
            <div class="sidebar-section">
                <div class="sidebar-section-title">Documents</div>

                <a href="<?= base_url('documents') ?>"
                    class="sidebar-link <?= isActive('documents', $currentSegment1) ? 'active' : '' ?>">
                    <svg class="sidebar-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" />
                    </svg>
                    <span>Mes Documents</span>
                </a>

                <a href="<?= base_url('statistics') ?>"
                    class="sidebar-link <?= isActive('statistics', $currentSegment1) ? 'active' : '' ?>">
                    <svg class="sidebar-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path
                            d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" />
                    </svg>
                    <span>Statistiques</span>
                </a>
            </div>

            <div class="sidebar-divider"></div>

            <!-- Profile & Settings -->
            <div class="sidebar-section">
                <div class="sidebar-section-title">Mon compte</div>

                <a href="<?= base_url('profile') ?>"
                    class="sidebar-link <?= isActive('profile', $currentSegment1) ? 'active' : '' ?>">
                    <svg class="sidebar-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                            clip-rule="evenodd" />
                    </svg>
                    <span>Mon Profil</span>
                </a>

                <a href="<?= base_url('notifications/preferences') ?>"
                    class="sidebar-link <?= isActive('notifications', $currentSegment1) ? 'active' : '' ?>">
                    <svg class="sidebar-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path
                            d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
                    </svg>
                    <span>Notifications</span>
                </a>
            </div>

            <div class="sidebar-section">
                <div class="sidebar-section-title">Paramètres</div>

                <a href="<?= base_url('settings/company') ?>"
                    class="sidebar-link sidebar-sublink <?= strpos($currentPath, 'settings/company') !== false && strpos($currentPath, 'legal') === false && strpos($currentPath, 'invoicing') === false ? 'active' : '' ?>">
                    <svg class="sidebar-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z"
                            clip-rule="evenodd" />
                    </svg>
                    <span>Entreprise</span>
                </a>

                <a href="<?= base_url('settings/company/invoicing') ?>"
                    class="sidebar-link sidebar-sublink <?= strpos($currentPath, 'invoicing') !== false ? 'active' : '' ?>">
                    <svg class="sidebar-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm2 10a1 1 0 10-2 0v3a1 1 0 102 0v-3zm2-3a1 1 0 011 1v5a1 1 0 11-2 0v-5a1 1 0 011-1zm4-1a1 1 0 10-2 0v7a1 1 0 102 0V8z"
                            clip-rule="evenodd" />
                    </svg>
                    <span>Facturation</span>
                </a>

                <a href="<?= base_url('settings/company/legal') ?>"
                    class="sidebar-link sidebar-sublink <?= strpos($currentPath, 'legal') !== false ? 'active' : '' ?>">
                    <svg class="sidebar-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 2a1 1 0 00-1 1v1a1 1 0 002 0V3a1 1 0 00-1-1zM4 4h3a3 3 0 006 0h3a2 2 0 012 2v9a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2zm2.5 7a1.5 1.5 0 100-3 1.5 1.5 0 000 3zm2.45 4a2.5 2.5 0 10-4.9 0h4.9zM12 9a1 1 0 100 2h3a1 1 0 100-2h-3zm-1 4a1 1 0 011-1h2a1 1 0 110 2h-2a1 1 0 01-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                    <span>Mentions légales</span>
                </a>

                <a href="<?= base_url('settings/company/documents') ?>"
                    class="sidebar-link sidebar-sublink <?= strpos($currentPath, 'documents') !== false ? 'active' : '' ?>">
                    <svg class="sidebar-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"
                            clip-rule="evenodd" />
                    </svg>
                    <span>Documents</span>
                </a>
            </div>

            <div class="sidebar-section">
                <div class="sidebar-section-title">Sécurité</div>

                <a href="<?= base_url('account/security') ?>"
                    class="sidebar-link sidebar-sublink <?= strpos($currentPath, 'account/security') !== false ? 'active' : '' ?>">
                    <svg class="sidebar-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    <span>Sécurité</span>
                </a>

                <a href="<?= base_url('account/login-history') ?>"
                    class="sidebar-link sidebar-sublink <?= strpos($currentPath, 'login-history') !== false ? 'active' : '' ?>">
                    <svg class="sidebar-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                            clip-rule="evenodd" />
                    </svg>
                    <span>Historique connexions</span>
                </a>

                <a href="<?= base_url('account/rgpd') ?>"
                    class="sidebar-link sidebar-sublink <?= strpos($currentPath, 'account/rgpd') !== false ? 'active' : '' ?>">
                    <svg class="sidebar-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                    <span>Mes données (RGPD)</span>
                </a>
            </div>

            <!-- Multi-Company Management -->
            <div class="sidebar-section">
                <div class="sidebar-section-title">Entreprises</div>

                <a href="<?= base_url('companies') ?>"
                    class="sidebar-link sidebar-sublink <?= strpos($currentPath, 'companies') !== false && strpos($currentPath, 'settings/company') === false ? 'active' : '' ?>">
                    <svg class="sidebar-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4z"
                            clip-rule="evenodd" />
                    </svg>
                    <span>Mes entreprises</span>
                </a>
            </div>

            <!-- Administration Section (Admin Only) -->
            <?php if (session()->get('user_role') === 'admin' || session()->get('role') === 'admin'): ?>
                <div class="sidebar-section">
                    <div class="sidebar-section-title">Administration</div>

                    <a href="<?= base_url('admin/dashboard') ?>"
                        class="sidebar-link sidebar-sublink <?= strpos($currentPath, 'admin/dashboard') !== false ? 'active' : '' ?>">
                        <svg class="sidebar-icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"
                                clip-rule="evenodd" />
                        </svg>
                        <span>Tableau de bord</span>
                    </a>

                    <a href="<?= base_url('admin/users') ?>"
                        class="sidebar-link sidebar-sublink <?= strpos($currentPath, 'admin/users') !== false ? 'active' : '' ?>">
                        <svg class="sidebar-icon" viewBox="0 0 20 20" fill="currentColor">
                            <path
                                d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                        </svg>
                        <span>Utilisateurs</span>
                    </a>

                    <a href="<?= base_url('admin/permissions') ?>"
                        class="sidebar-link sidebar-sublink <?= strpos($currentPath, 'admin/permissions') !== false ? 'active' : '' ?>">
                        <svg class="sidebar-icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        <span>Permissions</span>
                    </a>

                    <a href="<?= base_url('admin/audit') ?>"
                        class="sidebar-link sidebar-sublink <?= strpos($currentPath, 'admin/audit') !== false ? 'active' : '' ?>">
                        <svg class="sidebar-icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                clip-rule="evenodd" />
                        </svg>
                        <span>Journal d'audit</span>
                    </a>

                    <a href="<?= base_url('admin/dashboard/security') ?>"
                        class="sidebar-link sidebar-sublink <?= strpos($currentPath, 'dashboard/security') !== false ? 'active' : '' ?>">
                        <svg class="sidebar-icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                        <span>Alertes sécurité</span>
                    </a>
                </div>
            <?php endif; ?>

            <a href="<?= base_url('account/deletion') ?>"
                class="sidebar-link sidebar-sublink danger <?= strpos($currentPath, 'deletion') !== false ? 'active' : '' ?>">
                <svg class="sidebar-icon" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                        clip-rule="evenodd" />
                </svg>
                <span>Suppression compte</span>
            </a>
            </div>
        </nav>

        <!-- User Profile Section at Bottom -->
        <div class="sidebar-footer">
            <div class="user-profile-mini">
                <div class="user-avatar">
                    <?php
                    $userEmail = session()->get('user_email') ?? 'U';
                    $userInitial = strtoupper(substr($userEmail, 0, 1));
                    ?>
                    <span><?= $userInitial ?></span>
                </div>
                <div class="user-info">
                    <span class="user-name"><?= esc(session()->get('user_name') ?? 'Utilisateur') ?></span>
                    <span class="user-email"><?= esc(session()->get('user_email') ?? 'email@exemple.fr') ?></span>
                </div>
                <a href="<?= base_url('logout') ?>" class="logout-btn" title="Déconnexion">
                    <svg viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z"
                            clip-rule="evenodd" />
                    </svg>
                </a>
            </div>
        </div>
    </aside>

    <!-- Mobile Sidebar Toggle -->
    <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
        <svg viewBox="0 0 20 20" fill="currentColor" class="menu-icon">
            <path fill-rule="evenodd"
                d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                clip-rule="evenodd" />
        </svg>
        <svg viewBox="0 0 20 20" fill="currentColor" class="close-icon">
            <path fill-rule="evenodd"
                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                clip-rule="evenodd" />
        </svg>
    </button>

    <!-- Mobile Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Main Content Area -->
    <main class="main-content">
        <!-- Top Header Bar -->
        <header class="top-header">
            <div class="header-left">
                <h1 class="page-title">
                    <?= $this->renderSection('page_title', true) ?: $this->renderSection('title', true) ?>
                </h1>
            </div>
            <div class="header-right">
                <?php if (ENVIRONMENT === 'development'): ?>
                    <div class="dev-menu">
                        <button class="dev-menu-btn" onclick="this.nextElementSibling.classList.toggle('show')">
                            <svg viewBox="0 0 20 20" fill="currentColor" width="16" height="16">
                                <path fill-rule="evenodd"
                                    d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                            Dev
                        </button>
                        <div class="dev-dropdown">
                            <?php
                            $pages = get_dev_pages();
                            foreach ($pages as $name => $url): ?>
                                <a href="<?= $url ?>"><?= $name ?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="header-notifications">
                    <a href="<?= base_url('notifications/center') ?>" class="notification-btn" title="Notifications">
                        <svg viewBox="0 0 20 20" fill="currentColor">
                            <path
                                d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
                        </svg>
                        <span class="notification-badge" id="notification-badge" style="display: none;">0</span>
                    </a>
                </div>

                <div class="header-user">
                    <a href="<?= base_url('profile') ?>" class="user-header-link">
                        <div class="user-avatar-small">
                            <span><?= $userInitial ?></span>
                        </div>
                    </a>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <div class="content-wrapper">
            <!-- Flash Messages -->
            <?php if (session()->has('success')): ?>
                <div class="alert alert-success">
                    <svg viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    <span><?= session('success') ?></span>
                    <button class="alert-close" onclick="this.parentElement.remove()">×</button>
                </div>
            <?php endif; ?>

            <?php if (session()->has('error')): ?>
                <div class="alert alert-danger">
                    <svg viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd" />
                    </svg>
                    <span><?= session('error') ?></span>
                    <button class="alert-close" onclick="this.parentElement.remove()">×</button>
                </div>
            <?php endif; ?>

            <?php if (session()->has('errors')): ?>
                <div class="alert alert-danger">
                    <svg viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd" />
                    </svg>
                    <div>
                        <ul style="margin: 0; padding-left: 1rem;">
                            <?php foreach (session('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <button class="alert-close" onclick="this.parentElement.remove()">×</button>
                </div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>
        </div>
    </main>

    <!-- Scripts -->
    <script>
        // Sidebar toggle for mobile
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        function toggleSidebar() {
            sidebar.classList.toggle('open');
            sidebarToggle.classList.toggle('active');
            sidebarOverlay.classList.toggle('active');
            document.body.classList.toggle('sidebar-open');
        }

        sidebarToggle.addEventListener('click', toggleSidebar);
        sidebarOverlay.addEventListener('click', toggleSidebar);

        // Close sidebar on escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && sidebar.classList.contains('open')) {
                toggleSidebar();
            }
        });

        // Dev menu toggle
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.dev-menu')) {
                document.querySelectorAll('.dev-dropdown').forEach(d => d.classList.remove('show'));
            }
        });

        // Alert auto-dismiss
        document.querySelectorAll('.alert').forEach(function (alert) {
            setTimeout(function () {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(() => alert.remove(), 300);
            }, 5000);
        });

        // Fetch unread notifications count
        function updateNotificationCount() {
            fetch('<?= base_url('notifications/unread-count') ?>')
                .then(response => response.json())
                .then(data => {
                    const badge = document.getElementById('notification-badge');
                    if (data.count > 0) {
                        badge.textContent = data.count;
                        badge.style.display = 'flex';
                    } else {
                        badge.style.display = 'none';
                    }
                })
                .catch(error => console.error('Error fetching notifications:', error));
        }

        // Update count on load and every 60 seconds
        document.addEventListener('DOMContentLoaded', updateNotificationCount);
        setInterval(updateNotificationCount, 60000);
    </script>
    <?= $this->renderSection('scripts') ?>
</body>

</html>