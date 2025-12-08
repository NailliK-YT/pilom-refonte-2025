<?php

namespace App\Controllers;

use App\Models\FournisseurModel;
use CodeIgniter\HTTP\RedirectResponse;

class Fournisseurs extends BaseController
{
    protected $fournisseurModel;

    public function __construct()
    {
        $this->fournisseurModel = new FournisseurModel();
    }

    private function getCompanyId()
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

    public function index()
    {
        $companyId = $this->getCompanyId();
        if (!$companyId) {
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter');
        }

        $params = [
            'company_id' => $companyId,
            'keywords' => $this->request->getGet('search'),
            'sort_by' => $this->request->getGet('sort_by'),
            'sort_order' => $this->request->getGet('sort_order')
        ];

        $perPage = 20;
        $page = $this->request->getGet('page') ?? 1;
        $offset = ($page - 1) * $perPage;

        $fournisseurs = $this->fournisseurModel->searchFournisseurs($params, $perPage, $offset);
        $total = $this->fournisseurModel->countSearchResults($params);

        $data = [
            'title' => 'Gestion des Fournisseurs',
            'fournisseurs' => $fournisseurs,
            'pager' => $this->fournisseurModel->pager,
            'total' => $total,
            'filters' => $params,
            'perPage' => $perPage,
            'currentPage' => $page
        ];

        return view('fournisseurs/index', $data);
    }

    public function create()
    {
        if (!$this->getCompanyId()) {
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter');
        }

        return view('fournisseurs/form', [
            'title' => 'Nouveau Fournisseur',
            'fournisseur' => null
        ]);
    }

    public function store()
    {
        $companyId = $this->getCompanyId();
        if (!$companyId) {
            return redirect()->to('/login');
        }

        $rules = [
            'nom' => 'required|min_length[2]|max_length[255]',
            'email' => 'permit_empty|valid_email',
            'siret' => 'permit_empty|exact_length[14]|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'company_id' => $companyId,
            'nom' => $this->request->getPost('nom'),
            'adresse' => $this->request->getPost('adresse'),
            'contact' => $this->request->getPost('contact'),
            'email' => $this->request->getPost('email'),
            'telephone' => $this->request->getPost('telephone'),
            'siret' => $this->request->getPost('siret')
        ];

        if ($this->fournisseurModel->insert($data)) {
            return redirect()->to('/fournisseurs')->with('success', 'Fournisseur créé avec succès');
        }

        return redirect()->back()->withInput()->with('error', 'Erreur lors de la création');
    }

    public function edit($id)
    {
        $companyId = $this->getCompanyId();
        $fournisseur = $this->fournisseurModel->find($id);

        if (!$fournisseur || $fournisseur['company_id'] !== $companyId) {
            return redirect()->to('/fournisseurs')->with('error', 'Fournisseur non trouvé');
        }

        return view('fournisseurs/form', [
            'title' => 'Modifier Fournisseur',
            'fournisseur' => $fournisseur
        ]);
    }

    public function update($id)
    {
        $companyId = $this->getCompanyId();
        $fournisseur = $this->fournisseurModel->find($id);

        if (!$fournisseur || $fournisseur['company_id'] !== $companyId) {
            return redirect()->to('/fournisseurs')->with('error', 'Fournisseur non trouvé');
        }

        $rules = [
            'nom' => 'required|min_length[2]|max_length[255]',
            'email' => 'permit_empty|valid_email',
            'siret' => 'permit_empty|exact_length[14]|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'nom' => $this->request->getPost('nom'),
            'adresse' => $this->request->getPost('adresse'),
            'contact' => $this->request->getPost('contact'),
            'email' => $this->request->getPost('email'),
            'telephone' => $this->request->getPost('telephone'),
            'siret' => $this->request->getPost('siret')
        ];

        if ($this->fournisseurModel->update($id, $data)) {
            return redirect()->to('/fournisseurs')->with('success', 'Fournisseur modifié avec succès');
        }

        return redirect()->back()->withInput()->with('error', 'Erreur lors de la modification');
    }

    public function delete($id)
    {
        $companyId = $this->getCompanyId();
        $fournisseur = $this->fournisseurModel->find($id);

        if (!$fournisseur || $fournisseur['company_id'] !== $companyId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Fournisseur non trouvé']);
        }

        // Vérifier s'il y a des dépenses liées
        $totalDepenses = $this->fournisseurModel->getTotalDepenses($id);
        if ($totalDepenses > 0) {
             // Soft delete is enabled in model, so this is fine, but maybe warn user?
             // For now, just allow delete (soft delete)
        }

        if ($this->fournisseurModel->delete($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Fournisseur supprimé']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Erreur lors de la suppression']);
    }
}
