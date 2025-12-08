<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run()
    {
        // First, ensure we have a business sector
        $sector = $this->db->table('business_sectors')
            ->limit(1)
            ->get()
            ->getRow();

        if (!$sector) {
            // Create a default sector if none exists
            $sectorId = $this->generateUUID();
            $this->db->table('business_sectors')->insert([
                'id' => $sectorId,
                'name' => 'Services',
                'description' => 'Services aux entreprises',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            echo "✓ Secteur d'activité par défaut créé : Services\n";
        } else {
            $sectorId = $sector->id;
        }

        // Check if company already exists
        $existing = $this->db->table('companies')
            ->where('name', 'Pilom Tech')
            ->get()
            ->getRow();

        if ($existing) {
            echo "✓ L'entreprise de test existe déjà (Pilom Tech)\n";
            $companyId = $existing->id;
        } else {
            $companyId = $this->generateUUID();
            $data = [
                'id' => $companyId,
                'name' => 'Pilom Tech',
                'business_sector_id' => $sectorId,  // AJOUT: Secteur d'activité requis
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $this->db->table('companies')->insert($data);
            echo "✓ Entreprise de test créée : Pilom Tech\n";
        }

        // Link test user to this company
        $this->db->table('users')
            ->where('email', 'test@pilom.fr')
            ->update(['company_id' => $companyId]);
            
        echo "✓ Utilisateur test@pilom.fr lié à l'entreprise\n";

        // Create Admin User if not exists
        $admin = $this->db->table('users')
            ->where('email', 'admin@pilom.fr')
            ->get()
            ->getRow();

        if (!$admin) {
            $adminId = $this->generateUUID();
            $adminData = [
                'id' => $adminId,
                'email' => 'admin@pilom.fr',
                'password_hash' => password_hash('admin123', PASSWORD_DEFAULT),
                'company_id' => $companyId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $this->db->table('users')->insert($adminData);
            echo "✓ Utilisateur admin@pilom.fr créé et lié à l'entreprise\n";
        } else {
             $this->db->table('users')
            ->where('email', 'admin@pilom.fr')
            ->update(['company_id' => $companyId]);
            echo "✓ Utilisateur admin@pilom.fr lié à l'entreprise\n";
        }
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
