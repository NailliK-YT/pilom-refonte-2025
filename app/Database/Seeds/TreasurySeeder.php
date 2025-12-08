<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\TreasuryModel;
use App\Models\TreasuryAlertModel;

class TreasurySeeder extends Seeder
{
    public function run()
    {
        $treasuryModel = new TreasuryModel();
        $alertModel = new TreasuryAlertModel();

        // Get the first company
        $company = $this->db->table('companies')->limit(1)->get()->getRow();
        if (!$company) {
            return;
        }
        $companyId = $company->id;

        // Get a user
        $user = $this->db->table('users')->where('company_id', $companyId)->limit(1)->get()->getRow();
        $userId = $user ? $user->id : null;

        // Create some initial treasury entries
        $entries = [
            [
                'company_id' => $companyId,
                'type' => 'income',
                'category' => 'capital',
                'amount' => 5000.00,
                'description' => 'Apport initial capital',
                'transaction_date' => date('Y-m-d', strtotime('-30 days')),
                'created_by' => $userId
            ],
            [
                'company_id' => $companyId,
                'type' => 'expense',
                'category' => 'loyer',
                'amount' => 1200.00,
                'description' => 'Loyer Bureau - Mois précédent',
                'transaction_date' => date('Y-m-d', strtotime('-25 days')),
                'created_by' => $userId
            ],
            [
                'company_id' => $companyId,
                'type' => 'income',
                'category' => 'vente',
                'amount' => 2500.00,
                'description' => 'Vente Prestation Conseil',
                'transaction_date' => date('Y-m-d', strtotime('-15 days')),
                'created_by' => $userId
            ],
            [
                'company_id' => $companyId,
                'type' => 'expense',
                'category' => 'materiel',
                'amount' => 850.00,
                'description' => 'Achat Ordinateur Portable',
                'transaction_date' => date('Y-m-d', strtotime('-10 days')),
                'created_by' => $userId
            ],
            [
                'company_id' => $companyId,
                'type' => 'income',
                'category' => 'vente',
                'amount' => 1800.00,
                'description' => 'Acompte Projet Web',
                'transaction_date' => date('Y-m-d', strtotime('-2 days')),
                'created_by' => $userId
            ]
        ];

        foreach ($entries as $entry) {
            // Use model to ensure balance calculation logic runs
            $treasuryModel->insert($entry);
        }

        // Create some alerts
        $alerts = [
            [
                'company_id' => $companyId,
                'name' => 'Solde bas',
                'threshold_type' => 'below',
                'threshold_amount' => 1000.00,
                'is_active' => true
            ],
            [
                'company_id' => $companyId,
                'name' => 'Objectif mensuel',
                'threshold_type' => 'above',
                'threshold_amount' => 10000.00,
                'is_active' => true
            ]
        ];

        foreach ($alerts as $alert) {
            $alertModel->insert($alert);
        }
    }
}
