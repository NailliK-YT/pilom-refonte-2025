<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CleanupDepensesTables extends BaseCommand
{
    protected $group = 'Database';
    protected $name = 'db:cleanup-depenses';
    protected $description = 'Nettoie les tables du module dépenses pour permettre une réinitialisation';

    public function run(array $params)
    {
        $db = \Config\Database::connect();

        CLI::write('Nettoyage des tables du module dépenses...', 'yellow');

        try {
            // Drop tables in reverse order of dependencies
            $tables = [
                'historique_depenses',
                'depenses_recurrences',
                'depenses',
                'fournisseurs',
                'categories_depenses',
                'frequences',
                'products',
                'categories',
                'tva_rates',
            ];

            foreach ($tables as $table) {
                CLI::write("Suppression table: $table...", 'yellow');
                $db->query("DROP TABLE IF EXISTS $table CASCADE");
                CLI::write(" OK", 'green');
            }

            CLI::write("\nNettoyage table migrations...", 'yellow');
            $db->query("DELETE FROM migrations WHERE version LIKE '2025-12-05-14%' OR version LIKE '2025-12-04-10%'");
            CLI::write(" OK", 'green');

            CLI::write("\nToutes les tables ont été nettoyées!", 'green');

        } catch (\Exception $e) {
            CLI::error("Erreur: " . $e->getMessage());
        }
    }
}
