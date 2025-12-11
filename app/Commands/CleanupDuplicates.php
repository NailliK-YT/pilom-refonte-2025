<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

class CleanupDuplicates extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'db:cleanup-duplicates';
    protected $description = 'Remove duplicate business sectors from the database';

    public function run(array $params)
    {
        $db = Database::connect();
        
        CLI::write('=== Checking for duplicates ===', 'yellow');
        
        // Count before
        $query = $db->query('SELECT name, COUNT(*) as count FROM business_sectors GROUP BY name ORDER BY name');
        $results = $query->getResultArray();
        
        $hasDuplicates = false;
        foreach ($results as $row) {
            if ($row['count'] > 1) {
                $hasDuplicates = true;
                CLI::write("{$row['name']}: {$row['count']} entries (duplicate!)", 'red');
            } else {
                CLI::write("{$row['name']}: {$row['count']} entry", 'green');
            }
        }
        
        if (!$hasDuplicates) {
            CLI::write('No duplicates found!', 'green');
            return;
        }
        
        CLI::write("\n=== Removing duplicates ===", 'yellow');
        
        // Remove duplicates - keep only the oldest one (min ID)
        $deleteQuery = "
            DELETE FROM business_sectors a
            USING business_sectors b
            WHERE a.id > b.id
            AND a.name = b.name
        ";
        $db->query($deleteQuery);
        
        // Count after
        CLI::write("\n=== After cleanup ===", 'yellow');
        $query = $db->query('SELECT name, COUNT(*) as count FROM business_sectors GROUP BY name ORDER BY name');
        $results = $query->getResultArray();
        
        foreach ($results as $row) {
            CLI::write("{$row['name']}: {$row['count']} entry", 'green');
        }
        
        CLI::write("\nDuplicates removed successfully!", 'green');
    }
}
