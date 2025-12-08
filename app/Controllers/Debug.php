<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\CompanyModel;

class Debug extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        echo "<h1>Debug Insert</h1>";
        
        try {
            // 0. Get Business Sector
            $sector = $db->table('business_sectors')->get()->getRow();
            if (!$sector) {
                throw new \Exception("No business sectors found. Run BusinessSectorSeeder first.");
            }
            $sectorId = $sector->id;
            echo "Using Business Sector ID: " . $sectorId . "<br>";

            // 1. Insert Company
            $companyId = 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11';
            $companyData = [
                'id' => $companyId,
                'name' => 'Pilom Tech Debug',
                'business_sector_id' => $sectorId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            
            // Check if exists first
            $exists = $db->table('companies')->where('id', $companyId)->get()->getRow();
            if (!$exists) {
                $db->table('companies')->insert($companyData);
                echo "Company inserted.<br>";
            } else {
                echo "Company already exists.<br>";
            }

            // 2. Insert Admin
            $adminEmail = 'admin@pilom.fr';
            $adminExists = $db->table('users')->where('email', $adminEmail)->get()->getRow();
            
            if (!$adminExists) {
                $adminId = 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22';
                $adminData = [
                    'id' => $adminId,
                    'email' => $adminEmail,
                    'password_hash' => password_hash('admin123', PASSWORD_DEFAULT),
                    'company_id' => $companyId,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
                $db->table('users')->insert($adminData);
                echo "Admin inserted.<br>";
            } else {
                // Update company link
                $db->table('users')->where('email', $adminEmail)->update(['company_id' => $companyId]);
                echo "Admin updated with company link.<br>";
            }
            
            // 3. Link Test User
            $testEmail = 'test@pilom.fr';
            $db->table('users')->where('email', $testEmail)->update(['company_id' => $companyId]);
            echo "Test user updated with company link.<br>";

            // 4. Insert Company Settings
            $settingsExists = $db->table('company_settings')->where('company_id', $companyId)->get()->getRow();
            if (!$settingsExists) {
                $settingsData = [
                    'id' => 'c0eebc99-9c0b-4ef8-bb6d-6bb9bd380a33',
                    'company_id' => $companyId,
                    'country' => 'France',
                    'default_vat_rate' => 20.00,
                    'invoice_prefix' => 'INV',
                    'invoice_next_number' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
                $db->table('company_settings')->insert($settingsData);
                echo "Company Settings inserted.<br>";
            } else {
                echo "Company Settings already exist.<br>";
            }

        } catch (\Exception $e) {
            echo "<h2>Error</h2>";
            echo $e->getMessage();
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
        }
        
        echo "<h1>Current Data</h1>";
        $users = $db->table('users')->get()->getResultArray();
        echo "Users: <pre>" . print_r($users, true) . "</pre>";
        
        $companies = $db->table('companies')->get()->getResultArray();
        echo "Companies: <pre>" . print_r($companies, true) . "</pre>";
    }
}
