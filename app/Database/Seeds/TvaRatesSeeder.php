<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TvaRatesSeeder extends Seeder
{
    public function run()
    {
        $company = $this->db->table('companies')->get()->getFirstRow();
        
        if (!$company) {
            echo "⚠️  Aucune entreprise trouvée. Exécutez CompanySeeder d'abord.\n";
            return;
        }

        $rates = [
            [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'label' => 'Taux normal (20%)',
                'rate' => 20.00,
                'is_default' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'label' => 'Taux intermédiaire (10%)',
                'rate' => 10.00,
                'is_default' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'label' => 'Taux réduit (5,5%)',
                'rate' => 5.50,
                'is_default' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'label' => 'Taux super réduit (2,1%)',
                'rate' => 2.10,
                'is_default' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'label' => 'Exonéré (0%)',
                'rate' => 0.00,
                'is_default' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('tva_rates')->insertBatch($rates);
        echo "✓ " . count($rates) . " taux de TVA créés\n";
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


