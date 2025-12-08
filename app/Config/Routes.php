<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// ============================================
// PAGES PUBLIQUES (Informatives)
// ============================================
$routes->get('about', 'PagesController::about');
$routes->get('a-propos', 'PagesController::about');
$routes->match(['GET', 'POST'], 'contact', 'PagesController::contact');
$routes->get('faq', 'PagesController::faq');
$routes->get('mentions-legales', 'PagesController::legal');
$routes->get('cgu', 'PagesController::terms');
$routes->get('confidentialite', 'PagesController::privacy');
$routes->get('privacy', 'PagesController::privacy');

// Routes d'authentification (AuthController)
$routes->get('login', 'AuthController::login');
$routes->post('login/attempt', 'AuthController::attemptLogin');
$routes->get('logout', 'AuthController::logout');

// Password reset routes
$routes->get('forgot-password', 'AuthController::forgotPassword');
$routes->post('forgot-password', 'AuthController::sendResetLink');
$routes->get('reset-password/(:any)', 'AuthController::resetPassword/$1');
$routes->post('reset-password', 'AuthController::updatePassword');

// Company selection (for multi-company users)
$routes->get('select-company', 'CompanySwitchController::select', ['filter' => 'auth']);
$routes->post('switch-company/(:segment)', 'CompanySwitchController::switch/$1', ['filter' => 'auth']);
$routes->get('api/company/current', 'CompanySwitchController::current', ['filter' => 'auth']);

// ============================================
// MODULE ADMINISTRATION UTILISATEURS
// ============================================
$routes->group('admin/users', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'UsersController::index', ['filter' => 'role:users.view']);
    $routes->get('invite', 'UsersController::create', ['filter' => 'role:users.manage']);
    $routes->post('invite', 'UsersController::store', ['filter' => 'role:users.manage']);
    $routes->get('edit/(:segment)', 'UsersController::edit/$1', ['filter' => 'role:users.manage']);
    $routes->post('update/(:segment)', 'UsersController::update/$1', ['filter' => 'role:users.manage']);
    $routes->post('suspend/(:segment)', 'UsersController::suspend/$1', ['filter' => 'role:users.manage']);
    $routes->post('activate/(:segment)', 'UsersController::activate/$1', ['filter' => 'role:users.manage']);
    $routes->post('remove/(:segment)', 'UsersController::remove/$1', ['filter' => 'role:users.manage']);
});

// ============================================
// MODULE JOURNAL D'AUDIT
// ============================================
$routes->group('admin/audit', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'AuditController::index', ['filter' => 'role:users.view']);
    $routes->get('export', 'AuditController::exportCsv', ['filter' => 'role:users.view']);
    $routes->get('show/(:segment)', 'AuditController::show/$1', ['filter' => 'role:users.view']);
});

// ============================================
// MODULE TABLEAU DE BORD ADMIN
// ============================================
$routes->group('admin/dashboard', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'AdminDashboardController::index', ['filter' => 'role:users.view']);
    $routes->get('security', 'AdminDashboardController::security', ['filter' => 'role:users.view']);
});

// ============================================
// MODULE GESTION DES PERMISSIONS
// ============================================
$routes->group('admin/permissions', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'PermissionsController::index', ['filter' => 'role:users.manage']);
    $routes->get('create', 'PermissionsController::create', ['filter' => 'role:users.manage']);
    $routes->post('create', 'PermissionsController::store', ['filter' => 'role:users.manage']);
    $routes->get('copy/(:num)', 'PermissionsController::copy/$1', ['filter' => 'role:users.manage']);
    $routes->post('copy/(:num)', 'PermissionsController::storeCopy/$1', ['filter' => 'role:users.manage']);
    $routes->get('role/(:num)', 'PermissionsController::editRole/$1', ['filter' => 'role:users.manage']);
    $routes->post('role/(:num)', 'PermissionsController::updateRole/$1', ['filter' => 'role:users.manage']);
    $routes->post('delete/(:num)', 'PermissionsController::deleteRole/$1', ['filter' => 'role:users.manage']);
});

// User invitation acceptance (public route)
$routes->get('invitation/accept/(:any)', 'UsersController::acceptInvitation/$1');

// ============================================
// MODULE GESTION MULTI-ENTREPRISES
// ============================================
$routes->group('companies', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'CompanyManagementController::index');
    $routes->get('create', 'CompanyManagementController::create');
    $routes->post('create', 'CompanyManagementController::store');
    $routes->post('primary/(:any)', 'CompanyManagementController::setPrimary/$1');
    $routes->get('transfer/(:any)', 'CompanyManagementController::transferForm/$1');
    $routes->post('transfer/(:any)', 'CompanyManagementController::transfer/$1');
    $routes->post('leave/(:any)', 'CompanyManagementController::leave/$1');
});

// ============================================
// MODULE RGPD (Protection des données)
// ============================================
$routes->group('account/rgpd', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'RgpdController::index');
    $routes->get('export', 'RgpdController::exportMyData');
    $routes->post('delete', 'RgpdController::requestDeletion');
});

// Routes existantes
$routes->match(['GET', 'POST'], 'login-old', 'Auth::login');
$routes->get('logout-old', 'Auth::logout');
$routes->get('dashboard', 'Dashboard::index', ['filter' => 'auth']);

// Multi-step registration routes
$routes->match(['GET', 'POST'], 'register', 'Registration::step1');
$routes->match(['GET', 'POST'], 'register/step1', 'Registration::step1');
$routes->match(['GET', 'POST'], 'register/step2', 'Registration::step2');
$routes->get('register/step3', 'Registration::step3');
$routes->post('register/complete', 'Registration::complete');
$routes->get('register/success', 'Registration::success');

// AJAX validation endpoints
$routes->post('register/validate-email', 'Registration::validateEmailAjax');
$routes->post('register/validate-password', 'Registration::validatePasswordAjax');
$routes->post('register/save-progress', 'Registration::saveProgress');

// Email verification
$routes->get('verify-email/(:any)', 'Registration::verifyEmail/$1');

// ============================================
// MODULE GESTION PRODUITS & SERVICES
// ============================================

// Taux de TVA
$routes->group('tva-rates', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'TvaRates::index');
    $routes->match(['GET', 'POST'], 'create', 'TvaRates::create');
    $routes->match(['GET', 'POST'], 'edit/(:segment)', 'TvaRates::edit/$1');
    $routes->post('delete/(:segment)', 'TvaRates::delete/$1');
});

// Catégories
$routes->group('categories', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Categories::index');
    $routes->match(['GET', 'POST'], 'create', 'Categories::create');
    $routes->match(['GET', 'POST'], 'edit/(:segment)', 'Categories::edit/$1');
    $routes->post('delete/(:segment)', 'Categories::delete/$1');
    $routes->get('children/(:segment)', 'Categories::getChildren/$1'); // AJAX
});

// Produits
$routes->group('products', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Products::index');
    $routes->match(['GET', 'POST'], 'create', 'Products::create');
    $routes->get('show/(:segment)', 'Products::show/$1');
    $routes->match(['GET', 'POST'], 'edit/(:segment)', 'Products::edit/$1');
    $routes->post('archive/(:segment)', 'Products::archive/$1');
    $routes->post('bulk-archive', 'Products::bulkArchive');
    $routes->post('calculate-price', 'Products::calculatePrice'); // AJAX
});

// ============================================
// GESTION DU PROFIL ET PARAMÈTRES
// ============================================

// Profile management
$routes->group('profile', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'ProfileController::index');
    $routes->post('update', 'ProfileController::update');
    $routes->get('password', 'ProfileController::password');
    $routes->post('change-password', 'ProfileController::changePassword');
    $routes->post('upload-photo', 'ProfileController::uploadPhoto');
    $routes->post('delete-photo', 'ProfileController::deletePhoto');
});

// Company settings
$routes->group('settings/company', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'CompanySettingsController::index');
    $routes->post('update', 'CompanySettingsController::update');
    $routes->get('legal', 'CompanySettingsController::legal');
    $routes->post('update-legal', 'CompanySettingsController::updateLegal');
    $routes->get('invoicing', 'CompanySettingsController::invoicing');
    $routes->post('update-invoicing', 'CompanySettingsController::updateInvoicing');
    $routes->get('documents', 'CompanySettingsController::documents');
    $routes->post('update-documents', 'CompanySettingsController::updateDocuments');
    $routes->post('upload-logo', 'CompanySettingsController::uploadLogo');
    $routes->post('delete-logo', 'CompanySettingsController::deleteLogo');
});

// Account management
$routes->group('account', ['filter' => 'auth'], function ($routes) {
    $routes->get('security', 'AccountController::security');
    $routes->get('login-history', 'AccountController::loginHistory');
    $routes->get('deletion', 'AccountController::deletion');
    $routes->post('request-deletion', 'AccountController::requestDeletion');
    $routes->post('cancel-deletion', 'AccountController::cancelDeletion');
});

// Notifications
$routes->group('notifications', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'NotificationController::center');
    $routes->get('center', 'NotificationController::center');
    $routes->get('preferences', 'NotificationController::index');
    $routes->post('update-preferences', 'NotificationController::update');
    $routes->post('mark-read/(:segment)', 'NotificationController::markRead/$1');
    $routes->post('mark-all-read', 'NotificationController::markAllRead');
    $routes->get('unread-count', 'NotificationController::unreadCount');
    $routes->get('recent', 'NotificationController::recent');
    $routes->post('delete/(:segment)', 'NotificationController::delete/$1');
});

// ============================================
// MODULE GESTION DES DÉPENSES (F7)
// ============================================

// Dépenses - CRUD principal
$routes->group('depenses', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Depenses::index');
    $routes->get('create', 'Depenses::create');
    $routes->post('store', 'Depenses::store');
    $routes->get('show/(:segment)', 'Depenses::show/$1');
    $routes->get('edit/(:segment)', 'Depenses::edit/$1');
    $routes->post('update/(:segment)', 'Depenses::update/$1');
    $routes->post('delete/(:segment)', 'Depenses::delete/$1');
    $routes->post('archive/(:segment)', 'Depenses::archive/$1');
    $routes->post('calculate-tva', 'Depenses::calculateTVA'); // AJAX
});

// Catégories de dépenses
$routes->group('categories-depenses', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'CategoriesDepenses::index');
    $routes->post('create', 'CategoriesDepenses::create');
    $routes->post('update/(:segment)', 'CategoriesDepenses::update/$1');
    $routes->post('delete/(:segment)', 'CategoriesDepenses::delete/$1');
});

// Fournisseurs
$routes->group('fournisseurs', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Fournisseurs::index');
    $routes->get('create', 'Fournisseurs::create');
    $routes->post('store', 'Fournisseurs::store');
    $routes->get('show/(:segment)', 'Fournisseurs::show/$1');
    $routes->get('edit/(:segment)', 'Fournisseurs::edit/$1');
    $routes->post('update/(:segment)', 'Fournisseurs::update/$1');
    $routes->post('delete/(:segment)', 'Fournisseurs::delete/$1');
    $routes->post('quick-create', 'Fournisseurs::quickCreate'); // AJAX
    $routes->post('import-csv', 'Fournisseurs::importCSV');
});

// Dépenses récurrentes
$routes->group('depenses-recurrentes', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'DepensesRecurrentes::index');
    $routes->get('create', 'DepensesRecurrentes::create');
    $routes->post('store', 'DepensesRecurrentes::store');
    $routes->get('edit/(:segment)', 'DepensesRecurrentes::edit/$1');
    $routes->post('update/(:segment)', 'DepensesRecurrentes::update/$1');
    $routes->post('suspend/(:segment)', 'DepensesRecurrentes::suspend/$1');
    $routes->post('resume/(:segment)', 'DepensesRecurrentes::resume/$1');
    $routes->post('terminate/(:segment)', 'DepensesRecurrentes::terminate/$1');
});

// Statistiques
$routes->group('depenses-stats', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'DepensesStats::dashboard');
    $routes->get('by-category', 'DepensesStats::byCategory');
    $routes->get('by-period', 'DepensesStats::byPeriod');
    $routes->get('by-supplier', 'DepensesStats::bySupplier');
});

// ============================================
// MODULE TRÉSORERIE
// ============================================
$routes->group('treasury', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'TreasuryController::index');
    $routes->get('create', 'TreasuryController::create');
    $routes->post('store', 'TreasuryController::store');
    $routes->get('alerts', 'TreasuryController::alerts');
    $routes->post('alerts/store', 'TreasuryController::storeAlert');
    $routes->post('alerts/delete/(:segment)', 'TreasuryController::deleteAlert/$1');
    $routes->get('chart-data', 'TreasuryController::chartData');
});

// Exports
$routes->group('depenses-export', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'DepensesExport::index');
    $routes->post('comptable', 'DepensesExport::exportComptable');
    $routes->post('justificatifs', 'DepensesExport::downloadJustificatifs');
});


// ============================================
// MODULE COMMERCIAL (CRM & VENTES)
// ============================================

// Contacts
$routes->group('contacts', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'ContactController::index');
    $routes->match(['GET', 'POST'], 'create', 'ContactController::create');
    $routes->post('store', 'ContactController::store');
    $routes->match(['GET', 'POST'], 'edit/(:segment)', 'ContactController::edit/$1');
    $routes->post('update/(:segment)', 'ContactController::update/$1');
    $routes->post('delete/(:segment)', 'ContactController::delete/$1');
});

// Devis
$routes->get('/debug', 'Debug::index');

$routes->group('devis', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'DevisController::index');
    $routes->match(['GET', 'POST'], 'create', 'DevisController::create');
    $routes->post('store', 'DevisController::store');
    $routes->match(['GET', 'POST'], 'edit/(:segment)', 'DevisController::edit/$1');
    $routes->post('update/(:segment)', 'DevisController::update/$1');
    $routes->post('delete/(:segment)', 'DevisController::delete/$1');
    $routes->get('show/(:segment)', 'DevisController::show/$1');
    $routes->match(['GET', 'POST'], 'convertir-en-facture/(:segment)', 'DevisController::convertirEnFacture/$1');
});

// Factures
$routes->group('factures', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'FactureController::index');
    $routes->match(['GET', 'POST'], 'create', 'FactureController::create');
    $routes->post('store', 'FactureController::store');
    $routes->match(['GET', 'POST'], 'edit/(:segment)', 'FactureController::edit/$1');
    $routes->post('update/(:segment)', 'FactureController::update/$1');
    $routes->post('delete/(:segment)', 'FactureController::delete/$1');
    $routes->get('show/(:segment)', 'FactureController::show/$1');
    $routes->get('send/(:segment)', 'FactureController::send/$1');
    $routes->get('pdf/(:segment)', 'FactureController::pdf/$1');
    $routes->get('reminder/(:segment)', 'FactureController::reminder/$1');
});

// Règlements
$routes->group('reglements', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'ReglementController::index');
    $routes->match(['GET', 'POST'], 'create', 'ReglementController::create');
    $routes->post('store', 'ReglementController::store');
    $routes->match(['GET', 'POST'], 'edit/(:segment)', 'ReglementController::edit/$1');
    $routes->post('update/(:segment)', 'ReglementController::update/$1');
    $routes->post('delete/(:segment)', 'ReglementController::delete/$1');
});

// ============================================
// MODULE DOCUMENTS
// ============================================
$routes->group('documents', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'DocumentController::index');
    $routes->get('create-folder', 'DocumentController::createFolder');
    $routes->post('create-folder', 'DocumentController::createFolder');
    $routes->get('upload', 'DocumentController::upload');
    $routes->post('upload', 'DocumentController::upload');
    $routes->get('download/(:segment)', 'DocumentController::download/$1');
    $routes->post('delete/(:segment)', 'DocumentController::delete/$1');
    $routes->post('delete-folder/(:segment)', 'DocumentController::deleteFolder/$1');
    $routes->post('share/(:segment)', 'DocumentController::share/$1');
    $routes->get('search', 'DocumentController::search');
});
$routes->get('documents/shared/(:any)', 'DocumentController::shared/$1'); // Public share link

// ============================================
// STATISTIQUES & RAPPORTS
// ============================================
$routes->group('statistics', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'StatisticsController::index');
    $routes->get('revenue', 'StatisticsController::revenue');
    $routes->get('expenses', 'StatisticsController::expenses');
    $routes->get('margins', 'StatisticsController::margins');
    $routes->get('export', 'StatisticsController::exportCSV');
});

// ============================================
// CONTACTS AVANCÉS
// ============================================
$routes->group('contacts', ['filter' => 'auth'], function ($routes) {
    $routes->get('export-csv', 'ContactController::exportCSV');
    $routes->match(['GET', 'POST'], 'import-csv', 'ContactController::importCSV');
    $routes->post('note/(:segment)', 'ContactController::addNote/$1');
    $routes->post('delete-note/(:segment)', 'ContactController::deleteNote/$1');
    $routes->post('attachment/(:segment)', 'ContactController::uploadAttachment/$1');
    $routes->post('delete-attachment/(:segment)', 'ContactController::deleteAttachment/$1');
});

// ============================================
// FACTURES RÉCURRENTES
// ============================================
$routes->group('recurring-invoices', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'RecurringInvoiceController::index');
    $routes->match(['GET', 'POST'], 'create', 'RecurringInvoiceController::create');
    $routes->match(['GET', 'POST'], 'edit/(:segment)', 'RecurringInvoiceController::edit/$1');
    $routes->post('pause/(:segment)', 'RecurringInvoiceController::pause/$1');
    $routes->post('resume/(:segment)', 'RecurringInvoiceController::resume/$1');
    $routes->post('cancel/(:segment)', 'RecurringInvoiceController::cancel/$1');
});

// ============================================
// PAGES PUBLIQUES (FONCTIONNALITÉS & PROFILS)
// ============================================
$routes->group('fonctionnalites', function ($routes) {
    $routes->get('facturation', 'Page::feature_facturation');
    $routes->get('devis', 'Page::feature_devis');
    $routes->get('contacts', 'Page::feature_contacts');
    $routes->get('depenses', 'Page::feature_depenses');
    $routes->get('tresorerie', 'Page::feature_tresorerie');
});

$routes->group('pour', function ($routes) {
    $routes->get('artisan', 'Page::profile_artisan');
    $routes->get('consultant', 'Page::profile_consultant');
    $routes->get('freelance', 'Page::profile_freelance');
    $routes->get('pme', 'Page::profile_pme');
    $routes->get('auto-entrepreneur', 'Page::profile_auto_entrepreneur');
    $routes->get('profession-liberale', 'Page::profile_profession_liberale');
});

// ==========================================
// ROUTE CATCH-ALL POUR LES PAGES DYNAMIQUES
// ⚠️ DOIT ÊTRE EN DERNIER ⚠️
// ==========================================
$routes->get('(:any)', 'Page::view/$1');
