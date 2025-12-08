<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CompanySettingsSeeder extends Seeder
{
    public function run()
    {
        $companies = $this->db->table('companies')->get()->getResult();
        
        if (empty($companies)) {
            echo "⚠️  Aucune entreprise trouvée\n";
            return;
        }

        $settings = [];
        
        foreach ($companies as $company) {
            $settings[] = [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'address' => '123 rue de l\'Innovation',
                'postal_code' => '75001',
                'city' => 'Paris',
                'country' => 'France',
                'phone' => '01 23 45 67 89',
                'email' => 'contact@pilom.fr',
                'website' => 'https://www.pilom.fr',
                'siret' => '12345678900019',
                'vat_number' => 'FR12345678901',
                'logo' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }

        $this->db->table('company_settings')->ignore(true)->insertBatch($settings);
        echo "✓ " . count($settings) . " paramètres d'entreprise créés\n";
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

