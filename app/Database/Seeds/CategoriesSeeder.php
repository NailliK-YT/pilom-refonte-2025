<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    public function run()
    {
        $company = $this->db->table('companies')->get()->getFirstRow();
        
        if (!$company) {
            echo "⚠️  Aucune entreprise trouvée. Exécutez CompanySeeder d'abord.\n";
            return;
        }

        // Catégories principales
        $categories = [
            [
                'id' => $parentElectronique = $this->generateUUID(),
                'company_id' => $company->id,
                'name' => 'Électronique',
                'description' => 'Produits électroniques et high-tech',
                'parent_id' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'name' => 'Ordinateurs',
                'description' => 'PC portables, de bureau et accessoires',
                'parent_id' => $parentElectronique,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'name' => 'Smartphones & Tablettes',
                'description' => 'Téléphones et tablettes tactiles',
                'parent_id' => $parentElectronique,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'name' => 'Accessoires informatiques',
                'description' => 'Claviers, souris, écrans, etc.',
                'parent_id' => $parentElectronique,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],

            // Mobilier de bureau
            [
                'id' => $parentMobilier = $this->generateUUID(),
                'company_id' => $company->id,
                'name' => 'Mobilier de bureau',
                'description' => 'Bureaux, chaises et rangements',
                'parent_id' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'name' => 'Bureaux',
                'description' => 'Bureaux individuels et collectifs',
                'parent_id' => $parentMobilier,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'name' => 'Sièges',
                'description' => 'Chaises de bureau ergonomiques',
                'parent_id' => $parentMobilier,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],

            // Fournitures
            [
                'id' => $parentFournitures = $this->generateUUID(),
                'company_id' => $company->id,
                'name' => 'Fournitures de bureau',
                'description' => 'Papeterie et consommables',
                'parent_id' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'name' => 'Papeterie',
                'description' => 'Cahiers, carnets, bloc-notes',
                'parent_id' => $parentFournitures,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'name' => 'Consommables',
                'description' => 'Encre, toner, papier',
                'parent_id' => $parentFournitures,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],

            // Services
            [
                'id' => $parentServices = $this->generateUUID(),
                'company_id' => $company->id,
                'name' => 'Services',
                'description' => 'Prestations et abonnements',
                'parent_id' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'name' => 'Formation',
                'description' => 'Formations professionnelles',
                'parent_id' => $parentServices,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'name' => 'Conseil',
                'description' => 'Prestations de conseil',
                'parent_id' => $parentServices,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('categories')->insertBatch($categories);
        echo "✓ " . count($categories) . " catégories de produits créées\n";
    }

    private function generateUUID(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }
}

