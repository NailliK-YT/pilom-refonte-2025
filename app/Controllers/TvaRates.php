<?php

namespace App\Controllers;

use App\Models\TvaRateModel;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Contrôleur pour la gestion des taux de TVA
 * CRUD complet avec pagination et recherche
 */
class TvaRates extends BaseController
{
    protected TvaRateModel $tvaRateModel;

    public function __construct()
    {
        $this->tvaRateModel = new TvaRateModel();
    }

    /**
     * Récupère le company_id de l'utilisateur connecté
     */
    private function getCompanyId(): ?string
    {
        $companyId = session()->get('company_id');

        if (!$companyId) {
            $userId = session()->get('user_id');
            if ($userId) {
                $db = \Config\Database::connect();
                $user = $db->table('users')->where('id', $userId)->get()->getRow();
                if ($user && isset($user->company_id)) {
                    $companyId = $user->company_id;
                    session()->set('company_id', $companyId);
                }
            }
        }

        return $companyId;
    }

    /**
     * Liste paginée des taux de TVA avec recherche
     */
    public function index()
    {
        $companyId = $this->getCompanyId();
        if (!$companyId) {
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter');
        }

        $perPage = 20;
        $search = $this->request->getGet('search');

        // Build the query
        $builder = $this->tvaRateModel->where('company_id', $companyId);

        if ($search) {
            $builder->like('label', $search);
        }

        // Get paginated results
        $tvaRates = $builder->orderBy('rate', 'DESC')->paginate($perPage);

        $data = [
            'title' => 'Gestion des taux de TVA',
            'tvaRates' => $tvaRates,
            'pager' => $this->tvaRateModel->pager,
            'search' => $search
        ];

        return view('tva_rates/index', $data);
    }

    /**
     * Affiche le formulaire de création
     */
    public function create()
    {
        $companyId = $this->getCompanyId();
        if (!$companyId) {
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter');
        }

        if (strtolower($this->request->getMethod()) === 'get') {
            return view('tva_rates/form', [
                'title' => 'Nouveau taux de TVA',
                'tvaRate' => null,
                'validation' => \Config\Services::validation()
            ]);
        }

        return $this->store();
    }

    /**
     * Enregistre un nouveau taux de TVA
     */
    private function store(): RedirectResponse
    {
        $companyId = $this->getCompanyId();
        if (!$companyId) {
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter');
        }

        $rules = [
            'rate' => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
            'label' => 'required|min_length[2]|max_length[100]',
            'is_default' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $data = [
            'company_id' => $companyId,
            'rate' => $this->request->getPost('rate'),
            'label' => $this->request->getPost('label'),
            'is_default' => (bool) $this->request->getPost('is_default')
        ];

        // Log the data being inserted
        log_message('info', 'TVA data to insert: ' . json_encode($data));

        try {
            // Si ce taux est défini comme défaut, désactiver les autres
            if ($data['is_default']) {
                log_message('info', 'Disabling other default TVA rates for company: ' . $companyId);
                $this->tvaRateModel->where('is_default', true)
                    ->where('company_id', $companyId)
                    ->set(['is_default' => false])
                    ->update();
            }

            $insertId = $this->tvaRateModel->insert($data);

            if ($insertId) {
                log_message('info', 'TVA rate created successfully with ID: ' . $insertId);
                return redirect()->to('/tva-rates')
                    ->with('success', 'Taux de TVA créé avec succès');
            } else {
                $errors = $this->tvaRateModel->errors();
                log_message('error', 'TVA insertion failed - Validation errors: ' . json_encode($errors));
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Erreur de validation: ' . implode(', ', $errors));
            }
        } catch (\Exception $e) {
            log_message('error', 'Erreur création taux TVA: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création du taux de TVA: ' . $e->getMessage());
        }
    }

    /**
     * Affiche le formulaire d'édition
     * 
     * @param string $id ID du taux de TVA
     */
    public function edit(string $id)
    {
        $companyId = $this->getCompanyId();
        if (!$companyId) {
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter');
        }

        $tvaRate = $this->tvaRateModel->findForCompany($id, $companyId);

        if (!$tvaRate) {
            return redirect()->to('/tva-rates')
                ->with('error', 'Taux de TVA introuvable');
        }

        if (strtolower($this->request->getMethod()) === 'get') {
            return view('tva_rates/form', [
                'title' => 'Modifier le taux de TVA',
                'tvaRate' => $tvaRate,
                'validation' => \Config\Services::validation()
            ]);
        }

        return $this->update($id);
    }

    /**
     * Met à jour un taux de TVA
     * 
     * @param string $id ID du taux de TVA
     */
    private function update(string $id): RedirectResponse
    {
        $companyId = $this->getCompanyId();
        if (!$companyId) {
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter');
        }

        // Vérifier l'appartenance
        $tvaRate = $this->tvaRateModel->findForCompany($id, $companyId);
        if (!$tvaRate) {
            return redirect()->to('/tva-rates')->with('error', 'Taux de TVA introuvable');
        }

        $rules = [
            'rate' => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
            'label' => 'required|min_length[2]|max_length[100]',
            'is_default' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $data = [
            'rate' => $this->request->getPost('rate'),
            'label' => $this->request->getPost('label'),
            'is_default' => (bool) $this->request->getPost('is_default')
        ];

        try {
            // Si ce taux est défini comme défaut, désactiver les autres
            if ($data['is_default']) {
                $this->tvaRateModel->where('is_default', true)
                    ->where('company_id', $companyId)
                    ->where('id !=', $id)
                    ->set(['is_default' => false])
                    ->update();
            }

            $this->tvaRateModel->update($id, $data);

            return redirect()->to('/tva-rates')
                ->with('success', 'Taux de TVA modifié avec succès');
        } catch (\Exception $e) {
            log_message('error', 'Erreur modification taux TVA: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la modification du taux de TVA');
        }
    }

    /**
     * Supprime un taux de TVA
     * 
     * @param string $id ID du taux de TVA
     */
    public function delete(string $id): RedirectResponse
    {
        $companyId = $this->getCompanyId();
        if (!$companyId) {
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter');
        }

        // Vérifier l'appartenance
        $tvaRate = $this->tvaRateModel->findForCompany($id, $companyId);
        if (!$tvaRate) {
            return redirect()->to('/tva-rates')->with('error', 'Taux de TVA introuvable');
        }

        try {
            // Vérifier si le taux est utilisé par des produits
            $db = \Config\Database::connect();
            $builder = $db->table('products');
            $count = $builder->where('tva_id', $id)->countAllResults();

            if ($count > 0) {
                return redirect()->to('/tva-rates')
                    ->with('error', "Ce taux de TVA est utilisé par $count produit(s) et ne peut pas être supprimé");
            }

            $this->tvaRateModel->delete($id);

            return redirect()->to('/tva-rates')
                ->with('success', 'Taux de TVA supprimé avec succès');
        } catch (\Exception $e) {
            log_message('error', 'Erreur suppression taux TVA: ' . $e->getMessage());
            return redirect()->to('/tva-rates')
                ->with('error', 'Une erreur est survenue lors de la suppression du taux de TVA');
        }
    }
}
