<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\NotificationModel;

class NotificationSeeder extends Seeder
{
    public function run()
    {
        $notificationModel = new NotificationModel();

        // Get the first company
        $company = $this->db->table('companies')->limit(1)->get()->getRow();
        if (!$company) {
            return;
        }
        $companyId = $company->id;

        // Get a user
        $user = $this->db->table('users')->where('company_id', $companyId)->limit(1)->get()->getRow();
        if (!$user) {
            return;
        }
        $userId = $user->id;

        // Create some sample notifications
        $notifications = [
            [
                'user_id' => $userId,
                'company_id' => $companyId,
                'type' => 'system',
                'title' => 'Bienvenue sur Pilom',
                'message' => 'Votre compte a été créé avec succès. Bienvenue dans votre nouvel espace de gestion.',
                'is_read' => true,
                'read_at' => date('Y-m-d H:i:s', strtotime('-5 days')),
                'priority' => 'normal',
                'created_at' => date('Y-m-d H:i:s', strtotime('-5 days'))
            ],
            [
                'user_id' => $userId,
                'company_id' => $companyId,
                'type' => 'invoice',
                'title' => 'Facture F2024-001 créée',
                'message' => 'La facture F2024-001 pour le client "Client Test" a été générée.',
                'link' => '/factures',
                'is_read' => true,
                'read_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
                'priority' => 'normal',
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 days'))
            ],
            [
                'user_id' => $userId,
                'company_id' => $companyId,
                'type' => 'payment',
                'title' => 'Paiement reçu',
                'message' => 'Un paiement de 1 200,00 € a été enregistré pour la facture F2024-001.',
                'link' => '/reglements',
                'is_read' => false,
                'priority' => 'high',
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 hour'))
            ],
            [
                'user_id' => $userId,
                'company_id' => $companyId,
                'type' => 'alert',
                'title' => 'Rappel : Déclaration TVA',
                'message' => 'N\'oubliez pas de déclarer votre TVA avant le 20 du mois.',
                'is_read' => false,
                'priority' => 'normal',
                'created_at' => date('Y-m-d H:i:s', strtotime('-30 minutes'))
            ]
        ];

        foreach ($notifications as $notification) {
            $notificationModel->insert($notification);
        }
    }
}
