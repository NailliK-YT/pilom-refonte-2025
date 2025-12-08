<?php

// Bootstrap CodeIgniter
require_once 'vendor/autoload.php';
$app = require_once 'app/Config/Paths.php';
require_once 'system/Boot.php';

$db = \Config\Database::connect();

echo "Nettoyage des tables...\n";

try {
    // Drop tables in reverse order of dependencies
    $tables = [
        'historique_depenses',
        'depenses_recurrences',
        'depenses',
        'fournisseurs',
        'categories_depenses',
        'frequences',
        'tva_rates'
    ];

    foreach ($tables as $table) {
        echo "Suppression table: $table...";
        $db->query("DROP TABLE IF EXISTS $table CASCADE");
        echo " OK\n";
    }

    echo "\nNettoyage table migrations...";
    $db->query("DELETE FROM migrations WHERE batch >= 9");
    echo " OK\n";

    echo "\nToutes les tables ont été nettoyées!\n";

} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
}
