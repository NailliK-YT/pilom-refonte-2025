<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\CLI\CLI;

/**
 * Complete Database Seeder - Accepts custom credentials
 * Usage: php spark db:seed CompleteDatabaseSeeder [admin_email] [admin_password] [test_email] [test_password]
 * Example: php spark db:seed CompleteDatabaseSeeder admin@pilom.fr admin123 test@pilom.fr admin123
 */
class CompleteDatabaseSeeder extends Seeder
{
    private $adminEmail;
    private $adminPassword;
    private $testEmail;
    private $testPassword;

    public function run()
    {
        // Get command line arguments
        $args = func_get_args();

        // Set default or custom credentials
        $this->adminEmail = $args[0] ?? 'admin@pilom.fr';
        $this->adminPassword = $args[1] ?? 'admin123';
        $this->testEmail = $args[2] ?? 'test@pilom.fr';
        $this->testPassword = $args[3] ?? 'admin123';

        echo "\n";
        echo "═══════════════════════════════════════════════════════════════\n";
        echo "  PILOM - Initialisation complète de la base de données\n";
        echo "═══════════════════════════════════════════════════════════════\n\n";
        echo "📧 Admin: {$this->adminEmail}\n";
        echo "📧 Test: {$this->testEmail}\n\n";

        // 1. Secteurs d'activité (requis pour companies)
        echo "📋 Étape 1/20: Secteurs d'activité\n";
        $this->call('BusinessSectorSeeder');
        echo "\n";

        // 2. Entreprises et utilisateurs avec paramètres personnalisés
        echo "🏢 Étape 2/20: Entreprises et utilisateurs\n";
        $this->seedCompanyAndUsers();
        echo "\n";

        // 3. Taux de TVA (requis pour produits et dépenses)
        echo "💶 Étape 3/20: Taux de TVA\n";
        $this->call('TvaRatesSeeder');
        echo "\n";

        // 4. Catégories de produits
        echo "📦 Étape 4/20: Catégories de produits\n";
        $this->call('CategoriesSeeder');
        echo "\n";

        // 5. Produits
        echo "🛍️  Étape 5/20: Produits\n";
        $this->call('ProductSeeder');
        echo "\n";

        // 6. Paliers de prix
        echo "💰 Étape 6/20: Paliers de prix dégressifs\n";
        $this->call('PriceTiersSeeder');
        echo "\n";

        // 7. Contacts (requis pour devis/factures)
        echo "📇 Étape 7/20: Contacts\n";
        $this->call('ContactSeeder');
        echo "\n";

        // 8. Devis
        echo "📄 Étape 8/20: Devis\n";
        $this->call('DevisSeeder');
        echo "\n";

        // 9. Factures
        echo "🧾 Étape 9/20: Factures\n";
        $this->call('FactureSeeder');
        echo "\n";

        // 10. Règlements
        echo "💳 Étape 10/20: Règlements\n";
        $this->call('ReglementSeeder');
        echo "\n";

        // 11. Fréquences (requis pour dépenses récurrentes)
        echo "🔄 Étape 11/20: Fréquences\n";
        $this->call('FrequenceSeeder');
        echo "\n";

        // 12. Catégories de dépenses
        echo "📊 Étape 12/20: Catégories de dépenses\n";
        $this->call('CategoryDepenseSeeder');
        echo "\n";

        // 13. Fournisseurs
        echo "🏭 Étape 13/20: Fournisseurs\n";
        $this->call('FournisseurSeeder');
        echo "\n";

        // 14. Dépenses
        echo "💸 Étape 14/20: Dépenses\n";
        $this->call('DepenseSeeder');
        echo "\n";

        // 15. Profils utilisateurs
        echo "👤 Étape 15/20: Profils utilisateurs\n";
        $this->call('UserProfilesSeeder');
        echo "\n";

        // 16. Paramètres d'entreprise
        echo "⚙️  Étape 16/20: Paramètres d'entreprise\n";
        $this->call('CompanySettingsSeeder');
        echo "\n";

        // 17. Préférences de notifications
        echo "🔔 Étape 17/20: Préférences de notifications\n";
        $this->call('NotificationPreferencesSeeder');
        echo "\n";

        // 18. Trésorerie
        echo "💰 Étape 18/20: Trésorerie\n";
        $this->call('TreasurySeeder');
        echo "\n";

        // 19. Notifications
        echo "🔔 Étape 19/20: Notifications\n";
        $this->call('NotificationSeeder');
        echo "\n";

        // 20. Pages du site (optionnel)
        echo "📝 Étape 20/20: Pages du site\n";
        $this->call('PagesSeeder');
        echo "\n";

        echo "═══════════════════════════════════════════════════════════════\n";
        echo "  ✅ Initialisation terminée avec succès !\n";
        echo "═══════════════════════════════════════════════════════════════\n";
        echo "\n";
        echo "📌 Identifiants de connexion :\n";
        echo "   Email admin : {$this->adminEmail}\n";
        echo "   Mot de passe : {$this->adminPassword}\n\n";
        echo "   Email test  : {$this->testEmail}\n";
        echo "   Mot de passe : {$this->testPassword}\n";
        echo "\n";
        echo "🌐 Accédez à l'application : http://localhost:8081\n";
        echo "\n";
    }

    private function seedCompanyAndUsers()
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
                'business_sector_id' => $sectorId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $this->db->table('companies')->insert($data);
            echo "✓ Entreprise de test créée : Pilom Tech\n";
        }

        // Create or update test user
        $testUser = $this->db->table('users')
            ->where('email', $this->testEmail)
            ->get()
            ->getRow();

        if ($testUser) {
            $this->db->table('users')
                ->where('email', $this->testEmail)
                ->update([
                    'password_hash' => password_hash($this->testPassword, PASSWORD_DEFAULT),
                    'company_id' => $companyId,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            echo "✓ Utilisateur {$this->testEmail} mis à jour\n";
        } else {
            $testUserId = $this->generateUUID();
            $testUserData = [
                'id' => $testUserId,
                'email' => $this->testEmail,
                'password_hash' => password_hash($this->testPassword, PASSWORD_DEFAULT),
                'company_id' => $companyId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $this->db->table('users')->insert($testUserData);
            echo "✓ Utilisateur {$this->testEmail} créé\n";
        }

        // Create or update admin user
        $admin = $this->db->table('users')
            ->where('email', $this->adminEmail)
            ->get()
            ->getRow();

        if ($admin) {
            $this->db->table('users')
                ->where('email', $this->adminEmail)
                ->update([
                    'password_hash' => password_hash($this->adminPassword, PASSWORD_DEFAULT),
                    'company_id' => $companyId,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            echo "✓ Utilisateur {$this->adminEmail} mis à jour\n";
        } else {
            $adminId = $this->generateUUID();
            $adminData = [
                'id' => $adminId,
                'email' => $this->adminEmail,
                'password_hash' => password_hash($this->adminPassword, PASSWORD_DEFAULT),
                'company_id' => $companyId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $this->db->table('users')->insert($adminData);
            echo "✓ Utilisateur {$this->adminEmail} créé\n";
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
