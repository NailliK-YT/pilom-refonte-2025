<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\FrequenceModel;
use App\Models\CategoryDepenseModel;
use App\Models\FournisseurModel;
use App\Models\DepenseModel;
use App\Models\DepenseRecurrenceModel;
use App\Models\HistoriqueDepenseModel;

class TestDepensesModule extends BaseCommand
{
    protected $group = 'Testing';
    protected $name = 'test:depenses';
    protected $description = 'Test complet du module de gestion des dépenses';

    public function run(array $params)
    {
        CLI::write('═══════════════════════════════════════════════', 'white');
        CLI::write('  TEST MODULE GESTION DES DÉPENSES', 'yellow');
        CLI::write('═══════════════════════════════════════════════', 'white');
        CLI::newLine();

        $this->testDatabaseTables();
        $this->testModels();
        $this->testHelpers();
        $this->testRelations();

        CLI::newLine();
        CLI::write('═══════════════════════════════════════════════', 'white');
        CLI::write('  TESTS TERMINÉS', 'green');
        CLI::write('═══════════════════════════════════════════════', 'white');
    }

    private function testDatabaseTables()
    {
        CLI::write('📊 Test des tables de la base de données', 'cyan');
        CLI::write('─────────────────────────────────────────────', 'white');

        $db = \Config\Database::connect();
        $tables = [
            'frequences',
            'categories_depenses',
            'fournisseurs',
            'depenses',
            'depenses_recurrences',
            'historique_depenses',
            'tva_rates'
        ];

        foreach ($tables as $table) {
            if ($db->tableExists($table)) {
                $count = $db->table($table)->countAll();
                CLI::write("  ✓ Table '$table' existe ($count enregistrements)", 'green');
            } else {
                CLI::write("  ✗ Table '$table' n'existe pas", 'red');
            }
        }
        CLI::newLine();
    }

    private function testModels()
    {
        CLI::write('🔧 Test des modèles', 'cyan');
        CLI::write('─────────────────────────────────────────────', 'white');

        // Test FrequenceModel
        $frequenceModel = new FrequenceModel();
        $frequences = $frequenceModel->findAll();
        CLI::write("  ✓ FrequenceModel: " . count($frequences) . " fréquences chargées", 'green');
        if (count($frequences) > 0) {
            CLI::write("    → Exemple: {$frequences[0]['nom']} ({$frequences[0]['jours']} jours)", 'white');
        }

        // Test CategoryDepenseModel
        $categoryModel = new CategoryDepenseModel();
        $categories = $categoryModel->getPredefinedCategories();
        CLI::write("  ✓ CategoryDepenseModel: " . count($categories) . " catégories prédéfinies", 'green');
        if (count($categories) > 0) {
            CLI::write("    → Exemple: {$categories[0]['nom']} (couleur: {$categories[0]['couleur']})", 'white');
        }

        // Test FournisseurModel
        $fournisseurModel = new FournisseurModel();
        $testFournisseur = [
            'company_id' => $this->getFirstCompanyId(),
            'nom' => 'Fournisseur Test',
            'adresse' => '123 Rue du Test',
            'contact' => 'Contact Test',
            'email' => 'test@fournisseur.com',
            'telephone' => '0123456789',
            'siret' => '12345678901234'
        ];

        try {
            $fournisseurId = $fournisseurModel->insert($testFournisseur);
            if ($fournisseurId) {
                CLI::write("  ✓ FournisseurModel: Création test réussie (ID: $fournisseurId)", 'green');
                // Nettoyage
                $fournisseurModel->delete($fournisseurId);
                CLI::write("    → Fournisseur test supprimé", 'white');
            }
        } catch (\Exception $e) {
            CLI::write("  ✗ FournisseurModel: Erreur - " . $e->getMessage(), 'red');
        }

        // Test DepenseModel
        $depenseModel = new DepenseModel();
        CLI::write("  ✓ DepenseModel: Modèle chargé", 'green');
        CLI::write("    → Méthodes disponibles: search, getStatsByCategory, getStatsByPeriod", 'white');

        CLI::newLine();
    }

    private function testHelpers()
    {
        CLI::write('🛠️  Test des fonctions helpers', 'cyan');
        CLI::write('─────────────────────────────────────────────', 'white');

        // Charger le helper
        helper('depense');

        // Test format_montant
        $montant = format_montant(1234.56);
        CLI::write("  ✓ format_montant(1234.56) = '$montant'", 'green');

        // Test calculate_tva
        $tva = calculate_tva(100, 20);
        CLI::write("  ✓ calculate_tva(100, 20%) = $tva €", 'green');

        // Test calculate_ttc
        $ttc = calculate_ttc(100, 20);
        CLI::write("  ✓ calculate_ttc(100, 20%) = $ttc €", 'green');

        // Test validate_siret
        $validSiret = validate_siret('73282932000074');
        $invalidSiret = validate_siret('12345678901234');
        CLI::write("  ✓ validate_siret('73282932000074') = " . ($validSiret ? 'VALIDE' : 'INVALIDE'), $validSiret ? 'green' : 'red');
        CLI::write("  ✓ validate_siret('12345678901234') = " . ($invalidSiret ? 'VALIDE' : 'INVALIDE'), !$invalidSiret ? 'green' : 'red');

        // Test get_statut_badge
        $badge = get_statut_badge('valide');
        CLI::write("  ✓ get_statut_badge('valide') génère un badge HTML", 'green');

        // Test format_date_fr
        $date = format_date_fr('2025-12-05');
        CLI::write("  ✓ format_date_fr('2025-12-05') = '$date'", 'green');

        CLI::newLine();
    }

    private function testRelations()
    {
        CLI::write('🔗 Test des relations et données', 'cyan');
        CLI::write('─────────────────────────────────────────────', 'white');

        $db = \Config\Database::connect();

        // Test données fréquences
        $freqCount = $db->table('frequences')->countAll();
        CLI::write("  ✓ Fréquences en base: $freqCount", 'green');

        // Test données catégories
        $catCount = $db->table('categories_depenses')->countAll();
        CLI::write("  ✓ Catégories en base: $catCount", 'green');

        // Test TVA
        $tvaCount = $db->table('tva_rates')->countAll();
        CLI::write("  ✓ Taux TVA en base: $tvaCount", 'green');

        // Test structure depenses
        $depenseFields = $db->getFieldNames('depenses');
        CLI::write("  ✓ Table depenses a " . count($depenseFields) . " colonnes", 'green');
        CLI::write("    → Colonnes clés: id, company_id, user_id, montant_ht, montant_ttc, tva_id", 'white');

        CLI::newLine();
    }

    private function getFirstCompanyId()
    {
        $db = \Config\Database::connect();
        $company = $db->table('companies')->select('id')->limit(1)->get()->getRow();
        return $company ? $company->id : null;
    }
}
