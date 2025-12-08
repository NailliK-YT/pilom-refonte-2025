<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\FournisseurModel;
use App\Models\DepenseModel;
use App\Models\CategoryDepenseModel;
use App\Models\FrequenceModel;
use App\Models\DepenseRecurrenceModel;

class TestDepensesCrud extends BaseCommand
{
    protected $group = 'Testing';
    protected $name = 'test:depenses-crud';
    protected $description = 'Test CRUD complet des dépenses';

    private $createdIds = [];

    public function run(array $params)
    {
        CLI::write('═══════════════════════════════════════════════', 'white');
        CLI::write('  TEST CRUD COMPLET MODULE DÉPENSES', 'yellow');
        CLI::write('═══════════════════════════════════════════════', 'white');
        CLI::newLine();

        try {
            $this->testFournisseurCrud();
            $this->testDepenseCrud();
            $this->testRecurrenceCrud();
            $this->testSearchAndStats();
            $this->cleanup();

            CLI::newLine();
            CLI::write('✅ TOUS LES TESTS CRUD RÉUSSIS !', 'green');
        } catch (\Exception $e) {
            CLI::write('❌ ERREUR: ' . $e->getMessage(), 'red');
            CLI::write('Stack trace:', 'yellow');
            CLI::write($e->getTraceAsString(), 'white');
            $this->cleanup();
        }
    }

    private function testFournisseurCrud()
    {
        CLI::write('📦 Test CRUD Fournisseurs', 'cyan');
        CLI::write('─────────────────────────────────────────────', 'white');

        $model = new FournisseurModel();
        $companyId = $this->getFirstCompanyId();

        // CREATE
        $data = [
            'company_id' => $companyId,
            'nom' => 'Test Fournisseur SARL',
            'adresse' => '123 Avenue des Tests, 75001 Paris',
            'contact' => 'M. Jean Dupont',
            'email' => 'contact@testfournisseur.fr',
            'telephone' => '0142123456',
            'siret' => '73282932000074' // SIRET valide
        ];

        $id = $model->insert($data);
        $this->createdIds['fournisseur'] = $id;
        CLI::write("  ✓ CREATE: Fournisseur créé (ID: $id)", 'green');

        // READ
        $fournisseur = $model->find($id);
        CLI::write("  ✓ READ: Fournisseur récupéré - {$fournisseur['nom']}", 'green');

        // UPDATE
        $model->update($id, ['telephone' => '0142654321']);
        $updated = $model->find($id);
        CLI::write("  ✓ UPDATE: Téléphone modifié - {$updated['telephone']}", 'green');

        // SEARCH
        $results = $model->search(['nom' => 'Test']);
        CLI::write("  ✓ SEARCH: " . count($results) . " résultat(s) trouvé(s)", 'green');

        // STATS
        $stats = $model->getWithStats($companyId);
        CLI::write("  ✓ STATS: " . count($stats) . " fournisseur(s) avec statistiques", 'green');

        CLI::newLine();
    }

    private function testDepenseCrud()
    {
        CLI::write('💰 Test CRUD Dépenses', 'cyan');
        CLI::write('─────────────────────────────────────────────', 'white');

        $model = new DepenseModel();
        $companyId = $this->getFirstCompanyId();
        $userId = $this->getFirstUserId();
        $tvaId = $this->getFirstTvaId();
        $categorieId = $this->getFirstCategoryId();

        // CREATE avec calcul automatique TTC
        $data = [
            'company_id' => $companyId,
            'user_id' => $userId,
            'date' => date('Y-m-d'),
            'montant_ht' => 100.00,
            'montant_ttc' => 120.00, // Sera calculé automatiquement si TVA fournie
            'tva_id' => $tvaId,
            'description' => 'Achat matériel informatique - Test',
            'categorie_id' => $categorieId,
            'fournisseur_id' => $this->createdIds['fournisseur'] ?? null,
            'statut' => 'brouillon',
            'recurrent' => false,
            'methode_paiement' => 'virement'
        ];

        $id = $model->insert($data);
        $this->createdIds['depense'] = $id;
        CLI::write("  ✓ CREATE: Dépense créée (ID: $id)", 'green');

        // READ avec relations
        $depense = $model->getDepenseWithRelations($id);
        CLI::write("  ✓ READ: Dépense récupérée avec relations", 'green');
        CLI::write("    → Montant HT: {$depense['montant_ht']} €", 'white');
        CLI::write("    → Montant TTC: {$depense['montant_ttc']} €", 'white');
        if (isset($depense['categorie_nom'])) {
            CLI::write("    → Catégorie: {$depense['categorie_nom']}", 'white');
        }

        // UPDATE (va créer un historique)
        $model->update($id, [
            'statut' => 'valide',
            'montant_ht' => 150.00
        ]);
        CLI::write("  ✓ UPDATE: Dépense modifiée (statut: valide)", 'green');

        // Vérifier historique
        $histModel = new \App\Models\HistoriqueDepenseModel();
        $historique = $histModel->where('depense_id', $id)->findAll();
        CLI::write("  ✓ HISTORIQUE: " . count($historique) . " modification(s) enregistrée(s)", 'green');

        CLI::newLine();
    }

    private function testRecurrenceCrud()
    {
        CLI::write('🔄 Test Récurrences', 'cyan');
        CLI::write('─────────────────────────────────────────────', 'white');

        $model = new DepenseRecurrenceModel();
        $depenseId = $this->createdIds['depense'] ?? null;
        $frequenceId = $this->getFirstFrequenceId();

        if (!$depenseId || !$frequenceId) {
            CLI::write("  ⚠ Skipping: Dépense ou fréquence manquante", 'yellow');
            CLI::newLine();
            return;
        }

        // Mettre à jour la dépense pour la rendre récurrente
        $depenseModel = new DepenseModel();
        $depenseModel->update($depenseId, [
            'recurrent' => true,
            'frequence_id' => $frequenceId
        ]);

        // CREATE Récurrence
        $data = [
            'depense_id' => $depenseId,
            'date_debut' => date('Y-m-d'),
            'date_fin' => date('Y-m-d', strtotime('+1 year')),
            'prochaine_occurrence' => date('Y-m-d', strtotime('+30 days')),
            'statut' => 'actif'
        ];

        $id = $model->insert($data);
        $this->createdIds['recurrence'] = $id;
        CLI::write("  ✓ CREATE: Récurrence créée (ID: $id)", 'green');

        // Test génération occurrences
        $recurrence = $model->find($id);
        CLI::write("  ✓ Prochaine occurrence: {$recurrence['prochaine_occurrence']}", 'green');

        // Test suspend/resume
        $model->suspend($id);
        $suspended = $model->find($id);
        CLI::write("  ✓ SUSPEND: Statut = {$suspended['statut']}", 'green');

        $model->resume($id);
        $resumed = $model->find($id);
        CLI::write("  ✓ RESUME: Statut = {$resumed['statut']}", 'green');

        CLI::newLine();
    }

    private function testSearchAndStats()
    {
        CLI::write('📊 Test Recherche et Statistiques', 'cyan');
        CLI::write('─────────────────────────────────────────────', 'white');

        $model = new DepenseModel();
        $companyId = $this->getFirstCompanyId();

        // Test recherche
        $results = $model->search([
            'company_id' => $companyId,
            'statut' => 'valide'
        ]);
        CLI::write("  ✓ Recherche (statut=valide): " . count($results) . " résultat(s)", 'green');

        // Test stats par catégorie
        $statsCat = $model->getStatsByCategory($companyId);
        CLI::write("  ✓ Stats par catégorie: " . count($statsCat) . " catégorie(s)", 'green');
        if (count($statsCat) > 0) {
            $first = $statsCat[0];
            CLI::write("    → {$first['nom']}: {$first['total']} € ({$first['count']} dépenses)", 'white');
        }

        // Test stats par période
        $statsPeriod = $model->getStatsByPeriod($companyId, 'mois');
        CLI::write("  ✓ Stats par période (mois): " . count($statsPeriod) . " période(s)", 'green');

        // Test total
        $total = $model->getTotalExpenses($companyId);
        CLI::write("  ✓ Total dépenses: $total €", 'green');

        CLI::newLine();
    }

    private function cleanup()
    {
        CLI::write('🧹 Nettoyage des données de test', 'cyan');
        CLI::write('─────────────────────────────────────────────', 'white');

        // Supprimer dans l'ordre inverse de création
        if (isset($this->createdIds['recurrence'])) {
            $model = new DepenseRecurrenceModel();
            $model->delete($this->createdIds['recurrence']);
            CLI::write("  ✓ Récurrence supprimée", 'white');
        }

        if (isset($this->createdIds['depense'])) {
            $model = new DepenseModel();
            $model->delete($this->createdIds['depense'], true); // Hard delete
            CLI::write("  ✓ Dépense supprimée", 'white');
        }

        if (isset($this->createdIds['fournisseur'])) {
            $model = new FournisseurModel();
            $model->delete($this->createdIds['fournisseur'], true); // Hard delete
            CLI::write("  ✓ Fournisseur supprimé", 'white');
        }

        CLI::newLine();
    }

    private function getFirstCompanyId()
    {
        $db = \Config\Database::connect();
        $company = $db->table('companies')->select('id')->limit(1)->get()->getRow();
        return $company ? $company->id : null;
    }

    private function getFirstUserId()
    {
        $db = \Config\Database::connect();
        $user = $db->table('users')->select('id')->limit(1)->get()->getRow();
        return $user ? $user->id : null;
    }

    private function getFirstTvaId()
    {
        $db = \Config\Database::connect();
        $tva = $db->table('tva_rates')->select('id')->limit(1)->get()->getRow();
        return $tva ? $tva->id : null;
    }

    private function getFirstCategoryId()
    {
        $db = \Config\Database::connect();
        $cat = $db->table('categories_depenses')->select('id')->limit(1)->get()->getRow();
        return $cat ? $cat->id : null;
    }

    private function getFirstFrequenceId()
    {
        $db = \Config\Database::connect();
        $freq = $db->table('frequences')->select('id')->limit(1)->get()->getRow();
        return $freq ? $freq->id : null;
    }
}
