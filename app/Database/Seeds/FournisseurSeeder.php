<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FournisseurSeeder extends Seeder
{
    public function run()
    {
        $company = $this->db->table('companies')->get()->getFirstRow();
        
        if (!$company) {
            echo "⚠️  Aucune entreprise trouvée\n";
            return;
        }

        $fournisseurs = [
            [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'nom' => 'Amazon Business',
                'email' => 'pro@amazon.fr',
                'telephone' => '01 70 38 50 00',
                'adresse' => '67 Boulevard du Général Leclerc, 92110 Clichy',
                'siret' => '48782333100049',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'nom' => 'Boulanger Pro',
                'email' => 'pro@boulanger.com',
                'telephone' => '03 20 10 10 10',
                'adresse' => 'Avenue de la Motte, 59810 Lesquin',
                'siret' => '31190076000055',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'nom' => 'Office Depot',
                'email' => 'contact@officedepot.fr',
                'telephone' => '01 41 31 82 00',
                'adresse' => '3 Avenue du Centre, 78180 Montigny-le-Bretonneux',
                'siret' => '35267811000053',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'nom' => 'OVH',
                'email' => 'support@ovh.com',
                'telephone' => '09 72 10 10 07',
                'adresse' => '2 rue Kellermann, 59100 Roubaix',
                'siret' => '42476141900045',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'nom' => 'Microsoft France',
                'email' => 'info@microsoft.fr',
                'telephone' => '01 85 73 10 10',
                'adresse' => '39 quai du Président Roosevelt, 92130 Issy-les-Moulineaux',
                'siret' => '32739954000013',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'nom' => 'Total Energies',
                'email' => 'contact@totalenergies.fr',
                'telephone' => '09 70 80 69 69',
                'adresse' => '2 place Jean Millier, 92400 Courbevoie',
                'siret' => '54205118000058',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'nom' => 'AXA Assurances',
                'email' => 'pro@axa.fr',
                'telephone' => '01 55 92 32 00',
                'adresse' => '313 Terrasses de l\'Arche, 92000 Nanterre',
                'siret' => '31220978000097',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'nom' => 'Orange Business',
                'email' => 'entreprise@orange.fr',
                'telephone' => '09 69 36 80 00',
                'adresse' => '78 rue Olivier de Serres, 75015 Paris',
                'siret' => '38012986100034',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'nom' => 'La Poste Courrier',
                'email' => 'pro@laposte.fr',
                'telephone' => '36 31',
                'adresse' => '44 Boulevard de Vaugirard, 75015 Paris',
                'siret' => '35600000000048',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'company_id' => $company->id,
                'nom' => 'Cabinet Dubois Expertise',
                'email' => 'contact@dubois-expertise.fr',
                'telephone' => '01 42 68 53 00',
                'adresse' => '15 rue de la Paix, 75002 Paris',
                'siret' => '82954831900019',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('fournisseurs')->insertBatch($fournisseurs);
        echo "✓ " . count($fournisseurs) . " fournisseurs créés\n";
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

