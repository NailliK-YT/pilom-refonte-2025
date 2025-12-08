<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class NotificationPreferencesSeeder extends Seeder
{
    public function run()
    {
        $users = $this->db->table('users')->get()->getResult();
        
        if (empty($users)) {
            echo "⚠️  Aucun utilisateur trouvé\n";
            return;
        }

        $preferences = [];
        
        foreach ($users as $user) {
            $preferences[] = [
                'id' => $this->generateUUID(),
                'user_id' => $user->id,
                'email_notifications' => true,
                'email_invoices' => true,
                'email_quotes' => true,
                'email_payments' => true,
                'email_marketing' => false,
                'push_notifications' => true,
                'inapp_notifications' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }

        $this->db->table('notification_preferences')->ignore(true)->insertBatch($preferences);
        echo "✓ " . count($preferences) . " préférences de notification créées\n";
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

