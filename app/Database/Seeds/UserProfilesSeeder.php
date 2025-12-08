<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserProfilesSeeder extends Seeder
{
    public function run()
    {
        $users = $this->db->table('users')->get()->getResult();
        
        if (empty($users)) {
            echo "⚠️  Aucun utilisateur trouvé\n";
            return;
        }

        $profiles = [];
        $prenoms = ['Jean', 'Marie', 'Pierre', 'Sophie', 'Lucas'];
        $noms = ['Martin', 'Bernard', 'Dubois', 'Lambert', 'Moreau'];
        $fonctions = ['Gérant', 'Directeur commercial', 'Comptable', 'Responsable marketing', 'Développeur'];
        
        foreach ($users as $index => $user) {
            $profiles[] = [
                'id' => $this->generateUUID(),
                'user_id' => $user->id,
                'first_name' => $prenoms[$index % count($prenoms)],
                'last_name' => $noms[$index % count($noms)],
                'phone' => '0' . mt_rand(6, 7) . ' ' . str_pad(mt_rand(0, 99), 2, '0', STR_PAD_LEFT) . ' ' . 
                           str_pad(mt_rand(0, 99), 2, '0', STR_PAD_LEFT) . ' ' .
                           str_pad(mt_rand(0, 99), 2, '0', STR_PAD_LEFT) . ' ' .
                           str_pad(mt_rand(0, 99), 2, '0', STR_PAD_LEFT),
                'profile_photo' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }

        $this->db->table('user_profiles')->ignore(true)->insertBatch($profiles);
        echo "✓ " . count($profiles) . " profils utilisateurs créés\n";
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

