<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CategoryDepenseSeeder extends Seeder
{
    public function run()
    {
        $company = $this->db->table('companies')->get()->getFirstRow();
        
        if (!$company) {
            echo "⚠️  Aucune entreprise trouvée\n";
            return;
        }

        $categories = [
            [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'nom' => 'Fournitures de bureau',
                'couleur' => '#3498db',
                'description' => 'Papeterie, consommables informatiques',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'nom' => 'Déplacements',
                'couleur' => '#e74c3c',
                'description' => 'Frais de transport, carburant, parking',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'nom' => 'Repas & Restauration',
                'couleur' => '#f39c12',
                'description' => 'Restaurants, traiteur, pauses café',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'nom' => 'Marketing & Communication',
                'couleur' => '#9b59b6',
                'description' => 'Publicité, réseaux sociaux, impressions',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'nom' => 'Informatique & Logiciels',
                'couleur' => '#1abc9c',
                'description' => 'Abonnements SaaS, licences, matériel IT',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'nom' => 'Loyer & Charges',
                'couleur' => '#34495e',
                'description' => 'Loyer des locaux, électricité, eau, internet',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'nom' => 'Honoraires & Prestations',
                'couleur' => '#16a085',
                'description' => 'Expert-comptable, avocats, consultants',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'nom' => 'Assurances',
                'couleur' => '#2ecc71',
                'description' => 'Assurance RC, locaux, véhicules',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'nom' => 'Formation',
                'couleur' => '#27ae60',
                'description' => 'Formations professionnelles, certifications',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'nom' => 'Entretien & Réparations',
                'couleur' => '#e67e22',
                'description' => 'Maintenance matériel, réparations diverses',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'nom' => 'Frais bancaires',
                'couleur' => '#c0392b',
                'description' => 'Commissions, frais de gestion compte',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'nom' => 'Autres',
                'couleur' => '#95a5a6',
                'description' => 'Dépenses diverses non catégorisées',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('categories_depenses')->insertBatch($categories);
        echo "✓ " . count($categories) . " catégories de dépenses créées\n";
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

