<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Contrôleur pour la gestion des catégories
 * CRUD avec support de la hiérarchie parent/enfant
 */
class Categories extends BaseController
{
    protected CategoryModel $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
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
     * Affichage arborescent des catégories
     */
    public function index()
    {
        $companyId = $this->getCompanyId();
        if (!$companyId) {
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter');
        }

        $data = [
            'title' => 'Gestion des catégories',
            'categoryTree' => $this->categoryModel->getCategoryTree($companyId)
        ];

        return view('categories/index', $data);
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
            return view('categories/form', [
                'title' => 'Nouvelle catégorie',
                'category' => null,
                'categoriesForSelect' => $this->categoryModel->getForSelect($companyId),
                'validation' => \Config\Services::validation()
            ]);
        }

        return $this->store();
    }

    /**
     * Enregistre une nouvelle catégorie
     */
    private function store(): RedirectResponse
    {
        $companyId = $this->getCompanyId();
        if (!$companyId) {
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter');
        }

        $rules = [
            'name' => 'required|min_length[2]|max_length[255]',
            'description' => 'permit_empty|max_length[1000]',
            'parent_id' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $data = [
            'company_id' => $companyId,
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'parent_id' => $this->request->getPost('parent_id') ?: null
        ];

        try {
            $this->categoryModel->insert($data);

            return redirect()->to('/categories')
                ->with('success', 'Catégorie créée avec succès');
        } catch (\Exception $e) {
            log_message('error', 'Erreur création catégorie: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création de la catégorie');
        }
    }

    /**
     * Affiche le formulaire d'édition
     * 
     * @param string $id ID de la catégorie
     */
    public function edit(string $id)
    {
        $companyId = $this->getCompanyId();
        if (!$companyId) {
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter');
        }

        $category = $this->categoryModel->findForCompany($id, $companyId);

        if (!$category) {
            return redirect()->to('/categories')
                ->with('error', 'Catégorie introuvable');
        }

        if (strtolower($this->request->getMethod()) === 'get') {
            // Exclure la catégorie elle-même de la liste des parents possibles
            $allCategories = $this->categoryModel->where('company_id', $companyId)->findAll();
            $categoriesForSelect = [];

            foreach ($allCategories as $cat) {
                if ($cat['id'] !== $id) {
                    $categoriesForSelect[$cat['id']] = $cat['name'];
                }
            }

            return view('categories/form', [
                'title' => 'Modifier la catégorie',
                'category' => $category,
                'categoriesForSelect' => $categoriesForSelect,
                'validation' => \Config\Services::validation()
            ]);
        }

        return $this->update($id);
    }

    /**
     * Met à jour une catégorie
     * 
     * @param string $id ID de la catégorie
     */
    private function update(string $id): RedirectResponse
    {
        $companyId = $this->getCompanyId();
        if (!$companyId) {
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter');
        }

        // Vérifier l'appartenance
        $category = $this->categoryModel->findForCompany($id, $companyId);
        if (!$category) {
            return redirect()->to('/categories')->with('error', 'Catégorie introuvable');
        }

        $rules = [
            'name' => 'required|min_length[2]|max_length[255]',
            'description' => 'permit_empty|max_length[1000]',
            'parent_id' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $parentId = $this->request->getPost('parent_id') ?: null;

        // Vérifier qu'on ne crée pas une référence circulaire
        if ($parentId && $parentId === $id) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Une catégorie ne peut pas être son propre parent');
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'parent_id' => $parentId
        ];

        try {
            $this->categoryModel->update($id, $data);

            return redirect()->to('/categories')
                ->with('success', 'Catégorie modifiée avec succès');
        } catch (\Exception $e) {
            log_message('error', 'Erreur modification catégorie: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la modification de la catégorie');
        }
    }

    /**
     * Supprime une catégorie
     * 
     * @param string $id ID de la catégorie
     */
    public function delete(string $id): RedirectResponse
    {
        $companyId = $this->getCompanyId();
        if (!$companyId) {
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter');
        }

        // Vérifier l'appartenance
        $category = $this->categoryModel->findForCompany($id, $companyId);
        if (!$category) {
            return redirect()->to('/categories')->with('error', 'Catégorie introuvable');
        }

        try {
            // Vérifier si la catégorie a des sous-catégories
            $children = $this->categoryModel->getChildCategories($id);
            if (!empty($children)) {
                return redirect()->to('/categories')
                    ->with('error', 'Cette catégorie contient des sous-catégories et ne peut pas être supprimée');
            }

            // Vérifier si la catégorie est utilisée par des produits
            if ($this->categoryModel->hasProducts($id)) {
                return redirect()->to('/categories')
                    ->with('error', 'Cette catégorie est utilisée par des produits et ne peut pas être supprimée');
            }

            $this->categoryModel->delete($id);

            return redirect()->to('/categories')
                ->with('success', 'Catégorie supprimée avec succès');
        } catch (\Exception $e) {
            log_message('error', 'Erreur suppression catégorie: ' . $e->getMessage());
            return redirect()->to('/categories')
                ->with('error', 'Une erreur est survenue lors de la suppression de la catégorie');
        }
    }

    /**
     * Endpoint AJAX pour récupérer les sous-catégories
     * 
     * @param string $parentId ID de la catégorie parente
     */
    public function getChildren(string $parentId)
    {
        $children = $this->categoryModel->getChildCategories($parentId);
        return $this->response->setJSON($children);
    }
}
