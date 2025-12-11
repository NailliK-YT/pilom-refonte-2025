<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BusinessSectorSeeder extends Seeder
{
    public function run()
    {
        $sectors = [
            [
                'id' => $this->generateUUID(),
                'name' => 'Services aux entreprises',
                'description' => 'Conseil, formation, services professionnels',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'name' => 'Commerce & Distribution',
                'description' => 'Vente au détail, e-commerce, négoce',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'name' => 'Restauration & Hôtellerie',
                'description' => 'Restaurants, cafés, hôtels, traiteurs',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'name' => 'Bâtiment & Travaux Publics',
                'description' => 'Construction, rénovation, travaux',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'name' => 'Santé & Bien-être',
                'description' => 'Professions médicales, paramédicales, fitness',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'name' => 'Industrie & Fabrication',
                'description' => 'Production industrielle, artisanat',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'name' => 'Informatique & Technologies',
                'description' => 'Développement logiciel, IT, télécommunications',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'name' => 'Transport & Logistique',
                'description' => 'Transport de marchandises, livraison, stockage',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'name' => 'Agriculture & Agroalimentaire',
                'description' => 'Production agricole, transformation alimentaire',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'name' => 'Immobilier',
                'description' => 'Agences immobilières, gestion de biens',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        // Check if sectors already exist to prevent duplicates
        $existing = $this->db->table('business_sectors')->countAllResults();
        if ($existing > 0) {
            echo "⚠ Secteurs d'activité déjà existants ({$existing}), ignoré\n";
            return;
        }

        $this->db->table('business_sectors')->insertBatch($sectors);
        echo "✓ " . count($sectors) . " secteurs d'activité créés\n";
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

