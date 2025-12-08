<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * DatabaseSeeder - Seeder basique pour les données commerciales
 * 
 * Pour une initialisation complète, utilisez : php spark db:seed MasterSeeder
 */
class DatabaseSeeder extends Seeder
{
    public function run()
    {
        echo "\n⚠️  Vous utilisez le DatabaseSeeder basique.\n";
        echo "Pour une initialisation complète : php spark db:seed MasterSeeder\n\n";
        
        // IMPORTANT: CompanySeeder doit être appelé en premier
        $this->call('CompanySeeder');
        $this->call('ContactSeeder');
		$this->call('DevisSeeder');
		$this->call('FactureSeeder');
		$this->call('ReglementSeeder');
    }
}
