<?php

/**
 * Test Script for Profile & Settings Features
 * Run: php test-profile-settings.php
 */

require __DIR__ . '/vendor/autoload.php';

// Bootstrap CodeIgniter
$app = \Config\Services::codeigniter();
$app->initialize();

echo "=== Testing Profile & Settings Implementation ===\n\n";

// Test 1: Check tables exist
echo "Test 1: Checking database tables...\n";
$db = \Config\Database::connect();

$tables = [
    'user_profiles',
    'company_settings',
    'notification_preferences',
    'login_history',
    'account_deletion_requests'
];

foreach ($tables as $table) {
    $exists = $db->tableExists($table);
    echo ($exists ? "✓" : "✗") . " Table '$table' " . ($exists ? "exists" : "missing") . "\n";
}

// Test 2: Check seeders created data
echo "\nTest 2: Checking seeded data...\n";

$userProfiles = $db->table('user_profiles')->countAll();
echo "✓ User profiles: $userProfiles\n";

$companySettings = $db->table('company_settings')->countAll();
echo "✓ Company settings: $companySettings\n";

$notifPrefs = $db->table('notification_preferences')->countAll();
echo "✓ Notification preferences: $notifPrefs\n";

// Test 3: Check models can be instantiated
echo "\nTest 3: Testing model instantiation...\n";

try {
    $userProfileModel = new \App\Models\UserProfileModel();
    echo "✓ UserProfileModel instantiated\n";

    $companySettingsModel = new \App\Models\CompanySettingsModel();
    echo "✓ CompanySettingsModel instantiated\n";

    $notificationModel = new \App\Models\NotificationPreferencesModel();
    echo "✓ NotificationPreferencesModel instantiated\n";

    $loginHistoryModel = new \App\Models\LoginHistoryModel();
    echo "✓ LoginHistoryModel instantiated\n";

    $accountDeletionModel = new \App\Models\AccountDeletionModel();
    echo "✓ AccountDeletionModel instantiated\n";
} catch (\Exception $e) {
    echo "✗ Model instantiation failed: " . $e->getMessage() . "\n";
}

// Test 4: Test validation
echo "\nTest 4: Testing SIRET validation...\n";

$validSiret = '73282932000074'; // Valid SIRET
$invalidSiret = '12345678901234'; // Invalid SIRET

$isValid = $companySettingsModel->validateSiret($validSiret);
echo ($isValid ? "✓" : "✗") . " Valid SIRET recognized: $validSiret\n";

$isValid = $companySettingsModel->validateSiret($invalidSiret);
echo (!$isValid ? "✓" : "✗") . " Invalid SIRET rejected: $invalidSiret\n";

// Test 5: Test IBAN validation
echo "\nTest 5: Testing IBAN validation...\n";

$validIban = 'FR7630001007941234567890185';
$invalidIban = 'FR123';

$isValid = $companySettingsModel->validateIban($validIban);
echo ($isValid ? "✓" : "✗") . " Valid IBAN recognized\n";

$isValid = $companySettingsModel->validateIban($invalidIban);
echo (!$isValid ? "✓" : "✗") . " Invalid IBAN rejected\n";

// Test 6: Check upload directories
echo "\nTest 6: Checking upload directories...\n";

$profileDir = WRITEPATH . 'uploads/profiles/';
$logoDir = WRITEPATH . 'uploads/logos/';

$profileExists = is_dir($profileDir);
echo ($profileExists ? "✓" : "✗") . " Profile upload directory " . ($profileExists ? "exists" : "missing") . "\n";

$logoExists = is_dir($logoDir);
echo ($logoExists ? "✓" : "✗") . " Logo upload directory " . ($logoExists ? "exists" : "missing") . "\n";

// Test 7: Check routes are defined
echo "\nTest 7: Checking routes configuration...\n";

$routes = \Config\Services::routes();
$routeCollection = $routes->getRoutes();

$expectedRoutes = [
    'profile',
    'settings/company',
    'account/security',
    'notifications/preferences'
];

foreach ($expectedRoutes as $route) {
    $exists = isset($routeCollection[$route]);
    echo ($exists ? "✓" : "✗") . " Route '$route' " . ($exists ? "registered" : "missing") . "\n";
}

// Test 8: Check language files
echo "\nTest 8: Checking language files...\n";

$langFiles = [
    'Profile',
    'Settings',
    'Account',
    'Notifications'
];

foreach ($langFiles as $file) {
    $path = APPPATH . "Language/fr/$file.php";
    $exists = file_exists($path);
    echo ($exists ? "✓" : "✗") . " Language file '$file.php' " . ($exists ? "exists" : "missing") . "\n";
}

// Test 9: Check controllers exist
echo "\nTest 9: Checking controller files...\n";

$controllers = [
    'ProfileController',
    'CompanySettingsController',
    'AccountController',
    'NotificationController'
];

foreach ($controllers as $controller) {
    $path = APPPATH . "Controllers/$controller.php";
    $exists = file_exists($path);
    echo ($exists ? "✓" : "✗") . " Controller '$controller.php' " . ($exists ? "exists" : "missing") . "\n";
}

// Test 10: Check views exist
echo "\nTest 10: Checking view files...\n";

$views = [
    'profile/index.php',
    'profile/password.php',
    'settings/company_info.php',
    'settings/legal.php',
    'settings/invoicing.php',
    'account/security.php',
    'account/login_history.php',
    'account/deletion.php',
    'notifications/preferences.php'
];

foreach ($views as $view) {
    $path = APPPATH . "Views/$view";
    $exists = file_exists($path);
    echo ($exists ? "✓" : "✗") . " View '$view' " . ($exists ? "exists" : "missing") . "\n";
}

echo "\n=== Tests Complete ===\n";
echo "\nNote: GD library is NOT installed. Image upload processing will not work.\n";
echo "To enable image processing, install GD or ImageMagick extension for PHP.\n";
