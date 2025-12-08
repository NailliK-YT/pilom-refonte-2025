<?php

namespace App\Controllers;

use App\Models\DepenseModel;
use App\Models\CategoryDepenseModel;
use App\Models\FournisseurModel;
use App\Models\FrequenceModel;
use App\Models\TvaRateModel;
use App\Models\TreasuryModel;
use App\Libraries\FileUploadService;
use CodeIgniter\HTTP\RedirectResponse;

class Depenses extends BaseController
{
    protected $depenseModel;
    protected $categoryModel;
    protected $fournisseurModel;
    protected $frequenceModel;
    protected $tvaModel;
    protected $treasuryModel;
    protected $fileService;

    public function __construct()
    {
        $this->depenseModel = new DepenseModel();
        $this->categoryModel = new CategoryDepenseModel();
        $this->fournisseurModel = new FournisseurModel();
        $this->frequenceModel = new FrequenceModel();
        $this->tvaModel = new TvaRateModel();
        $this->treasuryModel = new TreasuryModel();
        $this->fileService = new FileUploadService();

        helper('depense');
    }

    /**
     * Get company ID for current user
     */
    private function getCompanyId()
    {
        // Check if already in session
        $companyId = session()->get('company_id');

        if (!$companyId) {
            // Get from user record
            $userId = session()->get('user_id');
            if ($userId) {
                $db = \Config\Database::connect();
                $user = $db->table('users')->where('id', $userId)->get()->getRow();
                if ($user && isset($user->company_id)) {
                    $companyId = $user->company_id;
                    // Store in session for future use
                    session()->set('company_id', $companyId);
                }
            }
        }

        return $companyId;
    }

    /**
     * Liste des dépenses
     */
    public function index()
    {
        $companyId = $this->getCompanyId();

        if (!$companyId) {
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter');
        }

        // Récupérer les filtres
        $filters = [
            'company_id' => $companyId,
            'search' => $this->request->getGet('search'),
            'categorie_id' => $this->request->getGet('categorie'),
            'fournisseur_id' => $this->request->getGet('fournisseur'),
            'statut' => $this->request->getGet('statut'),
            'date_debut' => $this->request->getGet('date_debut'),
            'date_fin' => $this->request->getGet('date_fin'),
            'methode_paiement' => $this->request->getGet('methode'),
        ];

        // Rechercher les dépenses avec pagination
        $perPage = 20;
        $depenses = $this->depenseModel->searchDepenses($filters, $perPage);

        // Données pour les filtres
        $data = [
            'title' => 'Gestion des Dépenses',
            'depenses' => $depenses ?? [],
            'pager' => $this->depenseModel->pager,
            'categories' => $this->categoryModel->findAll() ?? [],
            'fournisseurs' => $this->fournisseurModel->where('company_id', $companyId)->findAll() ?? [],
            'filters' => $filters,
            'totalDepenses' => $this->depenseModel->getTotalExpenses($companyId) ?? 0,
        ];

        return view('depenses/index', $data);
    }

    /**
     * Formulaire de création
     */
    public function create()
    {
        $companyId = $this->getCompanyId();

        if (!$companyId) {
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter');
        }

        $data = [
            'title' => 'Nouvelle Dépense',
            'categories' => $this->categoryModel->findAll(),
            'fournisseurs' => $this->fournisseurModel->where('company_id', $companyId)->findAll(),
            'tvaRates' => $this->tvaModel->findAll(),
            'frequences' => $this->frequenceModel->findAll(),
            'depense' => null,
        ];

        return view('depenses/form', $data);
    }

    /**
     * Enregistrement d'une nouvelle dépense
     */
    public function store()
    {
        $validation = \Config\Services::validation();

        $rules = [
            'date' => 'required|valid_date',
            'montant_ht' => 'required|decimal',
            'tva_id' => 'required',
            'categorie_id' => 'required',
            'description' => 'required|min_length[3]',
            'methode_paiement' => 'required|in_list[especes,cheque,virement,cb]',
            'statut' => 'permit_empty|in_list[brouillon,valide,archive]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Préparer les données
        $companyId = $this->getCompanyId();
        $data = [
            'company_id' => $companyId,
            'user_id' => session()->get('user_id'),
            'date' => $this->request->getPost('date'),
            'montant_ht' => $this->request->getPost('montant_ht'),
            'montant_ttc' => $this->request->getPost('montant_ttc'),
            'tva_id' => $this->request->getPost('tva_id'),
            'description' => $this->request->getPost('description'),
            'categorie_id' => $this->request->getPost('categorie_id'),
            'fournisseur_id' => $this->request->getPost('fournisseur_id') ?: null,
            'statut' => $this->request->getPost('statut') ?: 'brouillon',
            'recurrent' => $this->request->getPost('recurrent') ? true : false,
            'frequence_id' => $this->request->getPost('frequence_id') ?: null,
            'methode_paiement' => $this->request->getPost('methode_paiement'),
        ];

        // Gérer l'upload du justificatif
        $justificatif = $this->request->getFile('justificatif');
        if ($justificatif && $justificatif->isValid()) {
            $uploadResult = $this->fileService->uploadJustificatif($justificatif);
            if ($uploadResult['success']) {
                $data['justificatif_path'] = $uploadResult['path'];
            }
        }

        // Insérer la dépense
        $depenseId = $this->depenseModel->insert($data);

        if ($depenseId) {
            // Si récurrent, créer la récurrence
            if ($data['recurrent'] && $data['frequence_id']) {
                $recurrenceModel = new \App\Models\DepenseRecurrenceModel();
                $recurrenceModel->insert([
                    'depense_id' => $depenseId,
                    'date_debut' => $this->request->getPost('date_debut_recurrence') ?: date('Y-m-d'),
                    'date_fin' => $this->request->getPost('date_fin_recurrence'),
                    'prochaine_occurrence' => $this->request->getPost('date_debut_recurrence') ?: date('Y-m-d'),
                    'statut' => 'actif',
                ]);
            }

            // Si la dépense est validée, ajouter à la trésorerie
            if ($data['statut'] === 'valide') {
                $this->treasuryModel->addFromExpense(
                    $companyId,
                    (float) $data['montant_ttc'],
                    $depenseId,
                    $data['description'],
                    $data['date']
                );
                
                // Vérifier les alertes de trésorerie
                $alertModel = new \App\Models\TreasuryAlertModel();
                $alertModel->checkAlerts($companyId, $this->treasuryModel->getCurrentBalance($companyId));
            }

            return redirect()->to('/depenses')->with('success', 'Dépense créée avec succès');
        }

        return redirect()->back()->withInput()->with('error', 'Erreur lors de la création de la dépense');
    }

    /**
     * Affichage d'une dépense
     */
    public function show($id)
    {
        $depense = $this->depenseModel->getDepenseWithRelations($id);

        if (!$depense) {
            return redirect()->to('/depenses')->with('error', 'Dépense non trouvée');
        }

        // Vérifier que la dépense appartient à l'entreprise
        if ($depense['company_id'] !== $this->getCompanyId()) {
            return redirect()->to('/depenses')->with('error', 'Accès non autorisé');
        }

        // Récupérer l'historique
        $historiqueModel = new \App\Models\HistoriqueDepenseModel();
        $historique = $historiqueModel->getFormattedHistory($id);

        $data = [
            'title' => 'Détail Dépense',
            'depense' => $depense,
            'historique' => $historique,
        ];

        return view('depenses/show', $data);
    }

    /**
     * Formulaire d'édition
     */
    public function edit($id)
    {
        $companyId = $this->getCompanyId();
        $depense = $this->depenseModel->find($id);

        if (!$depense || $depense['company_id'] !== $companyId) {
            return redirect()->to('/depenses')->with('error', 'Dépense non trouvée');
        }

        $data = [
            'title' => 'Modifier Dépense',
            'categories' => $this->categoryModel->findAll(),
            'fournisseurs' => $this->fournisseurModel->where('company_id', $companyId)->findAll(),
            'tvaRates' => $this->tvaModel->findAll(),
            'frequences' => $this->frequenceModel->findAll(),
            'depense' => $depense,
        ];

        return view('depenses/form', $data);
    }

    /**
     * Mise à jour d'une dépense
     */
    public function update($id)
    {
        $companyId = $this->getCompanyId();
        $depense = $this->depenseModel->find($id);

        if (!$depense || $depense['company_id'] !== $companyId) {
            return redirect()->to('/depenses')->with('error', 'Dépense non trouvée');
        }

        $validation = \Config\Services::validation();

        $rules = [
            'date' => 'required|valid_date',
            'montant_ht' => 'required|decimal',
            'tva_id' => 'required',
            'categorie_id' => 'required',
            'description' => 'required|min_length[3]',
            'methode_paiement' => 'required|in_list[especes,cheque,virement,cb]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Préparer les nouvelles données
        $data = [
            'date' => $this->request->getPost('date'),
            'montant_ht' => $this->request->getPost('montant_ht'),
            'montant_ttc' => $this->request->getPost('montant_ttc'),
            'tva_id' => $this->request->getPost('tva_id'),
            'description' => $this->request->getPost('description'),
            'categorie_id' => $this->request->getPost('categorie_id'),
            'fournisseur_id' => $this->request->getPost('fournisseur_id') ?: null,
            'statut' => $this->request->getPost('statut') ?: 'brouillon',
            'methode_paiement' => $this->request->getPost('methode_paiement'),
        ];

        // Gérer le nouveau justificatif
        $justificatif = $this->request->getFile('justificatif');
        if ($justificatif && $justificatif->isValid()) {
            // Supprimer l'ancien si existe
            if ($depense['justificatif_path']) {
                $this->fileService->deleteJustificatif($depense['justificatif_path']);
            }

            $uploadResult = $this->fileService->uploadJustificatif($justificatif);
            if ($uploadResult['success']) {
                $data['justificatif_path'] = $uploadResult['path'];
            }
        }

        // Mettre à jour (l'historique sera créé automatiquement)
        if ($this->depenseModel->update($id, $data)) {
            return redirect()->to('/depenses/show/' . $id)->with('success', 'Dépense modifiée avec succès');
        }

        return redirect()->back()->withInput()->with('error', 'Erreur lors de la modification');
    }

    /**
     * Suppression (soft delete)
     */
    public function delete($id)
    {
        $companyId = session()->get('company_id');
        $depense = $this->depenseModel->find($id);

        if (!$depense || $depense['company_id'] !== $companyId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Dépense non trouvée']);
        }

        if ($this->depenseModel->delete($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Dépense supprimée']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Erreur lors de la suppression']);
    }

    /**
     * Archiver une dépense
     */
    public function archive($id)
    {
        $companyId = session()->get('company_id');
        $depense = $this->depenseModel->find($id);

        if (!$depense || $depense['company_id'] !== $companyId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Dépense non trouvée']);
        }

        if ($this->depenseModel->archiveDepense($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Dépense archivée']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Erreur lors de l\'archivage']);
    }

    /**
     * Calcul AJAX du montant TTC
     */
    public function calculateTVA()
    {
        $montantHT = $this->request->getPost('montant_ht');
        $tvaId = $this->request->getPost('tva_id');

        if (!$montantHT || !$tvaId) {
            return $this->response->setJSON(['success' => false]);
        }

        $tva = $this->tvaModel->find($tvaId);
        if (!$tva) {
            return $this->response->setJSON(['success' => false]);
        }

        $montantTVA = ($montantHT * $tva['rate']) / 100;
        $montantTTC = $montantHT + $montantTVA;

        return $this->response->setJSON([
            'success' => true,
            'montant_tva' => number_format($montantTVA, 2, '.', ''),
            'montant_ttc' => number_format($montantTTC, 2, '.', ''),
        ]);
    }
}
