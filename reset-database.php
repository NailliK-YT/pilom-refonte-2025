<?php

/**
 * Script pour réinitialiser complètement la base de données
 * Ce script supprime toutes les tables et les recrée via les migrations
 */

// Define path constants
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);

// Bootstrap CodeIgniter
require FCPATH . '../app/Config/Paths.php';
$paths = new Config\Paths();
require $paths->systemDirectory . '/Boot.php';

// Boot the application
CodeIgniter\Boot::bootWeb($paths);

$db = \Config\Database::connect();

echo "====================================\n";
echo "RÉINITIALISATION DE LA BASE DE DONNÉES\n";
echo "====================================\n\n";

try {
    // Étape 1: Supprimer toutes les tables avec CASCADE
    echo "Étape 1: Suppression de toutes les tables...\n";
    echo "-------------------------------------------\n";
    
    $tables = [
        // Tables de relations (doivent être supprimées en premier)
        'reglement',
        'facture',
        'devis',
        'historique_depenses',
        'depenses_recurrences',
        'depenses',
        'invoice_items',
        'quote_items',
        'payments',
        'invoices',
        'quotes',
        
        // Tables principales
        'products',
        'price_tiers',
        'categories',
        'fournisseurs',
        'categories_depenses',
        'frequences',
        'tva_rates',
        'contact',
        'pages',
        
        // Tables utilisateurs et profils
        'account_deletion_requests',
        'login_history',
        'notification_preferences',
        'company_settings',
        'user_profiles',
        'users',
        
        // Tables de configuration
        'registration_sessions',
        'companies',
        'business_sectors',
        
        // Table de migrations
        'migrations'
    ];

    foreach ($tables as $table) {
        echo "  - Suppression de la table '$table'...";
        $db->query("DROP TABLE IF EXISTS $table CASCADE");
        echo " ✓\n";
    }
    
    echo "\nÉtape 2: Recréation de la table migrations...\n";
    echo "-------------------------------------------\n";
    
    // Recréer la table migrations
    $db->query("
        CREATE TABLE migrations (
            id BIGSERIAL PRIMARY KEY,
            version VARCHAR(255) NOT NULL,
            class VARCHAR(255) NOT NULL,
            \"group\" VARCHAR(255) NOT NULL,
            namespace VARCHAR(255) NOT NULL,
            time INTEGER NOT NULL,
            batch INTEGER NOT NULL
        )
    ");
    echo "  ✓ Table migrations créée\n";
    
    echo "\n====================================\n";
    echo "Base de données nettoyée avec succès!\n";
    echo "====================================\n\n";
    echo "Prochaine étape: Exécutez 'php spark migrate' pour recréer toutes les tables\n\n";

} catch (Exception $e) {
    echo "\n❌ ERREUR: " . $e->getMessage() . "\n";
    echo "\nTrace:\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}

