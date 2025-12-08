<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Master Seeder - Lance tous les seeders dans le bon ordre
 * Utilisez : php spark db:seed MasterSeeder
 */
class MasterSeeder extends Seeder
{
    public function run()
    {
        echo "\n";
        echo "═══════════════════════════════════════════════════════════════\n";
        echo "  PILOM - Initialisation complète de la base de données\n";
        echo "═══════════════════════════════════════════════════════════════\n\n";

        // 1. Secteurs d'activité (requis pour companies)
        echo "📋 Étape 1/15: Secteurs d'activité\n";
        $this->call('BusinessSectorSeeder');
        echo "\n";

        // 2. Entreprises et utilisateurs (requis pour tout le reste)
        echo "🏢 Étape 2/15: Entreprises et utilisateurs\n";
        $this->call('CompanySeeder');
        echo "\n";

        // 3. Taux de TVA (requis pour produits et dépenses)
        echo "💶 Étape 3/15: Taux de TVA\n";
        $this->call('TvaRatesSeeder');
        echo "\n";

        // 4. Catégories de produits
        echo "📦 Étape 4/15: Catégories de produits\n";
        $this->call('CategoriesSeeder');
        echo "\n";

        // 5. Produits
        echo "🛍️  Étape 5/15: Produits\n";
        $this->call('ProductSeeder');
        echo "\n";

        // 6. Paliers de prix
        echo "💰 Étape 6/15: Paliers de prix dégressifs\n";
        $this->call('PriceTiersSeeder');
        echo "\n";

        // 7. Contacts (requis pour devis/factures)
        echo "📇 Étape 7/15: Contacts\n";
        $this->call('ContactSeeder');
        echo "\n";

        // 8. Devis
        echo "📄 Étape 8/15: Devis\n";
        $this->call('DevisSeeder');
        echo "\n";

        // 9. Factures
        echo "🧾 Étape 9/15: Factures\n";
        $this->call('FactureSeeder');
        echo "\n";

        // 10. Règlements
        echo "💳 Étape 10/15: Règlements\n";
        $this->call('ReglementSeeder');
        echo "\n";

        // 11. Fréquences (requis pour dépenses récurrentes)
        echo "🔄 Étape 11/15: Fréquences\n";
        $this->call('FrequenceSeeder');
        echo "\n";

        // 12. Catégories de dépenses
        echo "📊 Étape 12/15: Catégories de dépenses\n";
        $this->call('CategoryDepenseSeeder');
        echo "\n";

        // 13. Fournisseurs
        echo "🏭 Étape 13/15: Fournisseurs\n";
        $this->call('FournisseurSeeder');
        echo "\n";

        // 14. Dépenses
        echo "💸 Étape 14/15: Dépenses\n";
        $this->call('DepenseSeeder');
        echo "\n";

        // 15. Profils utilisateurs
        echo "👤 Étape 15/15: Profils utilisateurs\n";
        $this->call('UserProfilesSeeder');
        echo "\n";

        // 16. Paramètres d'entreprise
        echo "⚙️  Étape 16/15: Paramètres d'entreprise\n";
        $this->call('CompanySettingsSeeder');
        echo "\n";

        // 17. Préférences de notifications
        echo "🔔 Étape 17/15: Préférences de notifications\n";
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

        // 20. Pages du site (optionnel - désactivé pour l'instant)
        // echo "📝 Étape 20/20: Pages du site\n";
        // $this->call('PagesSeeder');
        // echo "\n";

        echo "═══════════════════════════════════════════════════════════════\n";
        echo "  ✅ Initialisation terminée avec succès !\n";
        echo "═══════════════════════════════════════════════════════════════\n";
        echo "\n";
        echo "📌 Identifiants de test :\n";
        echo "   Email admin : admin@pilom.fr\n";
        echo "   Email test  : test@pilom.fr\n";
        echo "   Mot de passe : admin123\n";
        echo "\n";
        echo "🌐 Accédez à l'application : http://localhost:8081\n";
        echo "\n";
    }
}

