<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Modèle pour la gestion des catégories de produits/services
 * Supporte une structure hiérarchique avec parent_id
 */
class CategoryModel extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'id',
        'company_id',
        'name',
        'description',
        'parent_id'
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'name' => 'required|min_length[2]|max_length[255]',
        'description' => 'permit_empty|max_length[1000]',
        'parent_id' => 'permit_empty|is_not_unique[categories.id]'
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'Le nom de la catégorie est obligatoire',
            'min_length' => 'Le nom doit contenir au moins 2 caractères',
            'max_length' => 'Le nom ne peut pas dépasser 255 caractères'
        ],
        'parent_id' => [
            'is_not_unique' => 'La catégorie parente sélectionnée n\'existe pas'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateId'];
    protected $beforeUpdate = [];

    /**
     * Generate UUID for new records
     */
    protected function generateId(array $data)
    {
        if (!isset($data['data']['id'])) {
            $data['data']['id'] = $this->generateUUID();
        }

        return $data;
    }

    /**
     * Generate a UUID v4
     */
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

    /**
     * Récupère toutes les catégories sous forme d'arborescence pour une entreprise
     * 
     * @param string|null $companyId ID de l'entreprise
     * @return array Arborescence des catégories
     */
    public function getCategoryTree(?string $companyId = null): array
    {
        $builder = $this;
        if ($companyId) {
            $builder = $builder->where('company_id', $companyId);
        }
        $allCategories = $builder->orderBy('name', 'ASC')->findAll();
        return $this->buildTree($allCategories);
    }

    /**
     * Construit une structure hiérarchique à partir d'une liste plate
     * 
     * @param array $categories Liste de catégories
     * @param string|null $parentId ID du parent
     * @return array Arborescence
     */
    private function buildTree(array $categories, ?string $parentId = null): array
    {
        $branch = [];

        foreach ($categories as $category) {
            if ($category['parent_id'] === $parentId) {
                $children = $this->buildTree($categories, $category['id']);
                if ($children) {
                    $category['children'] = $children;
                }
                $branch[] = $category;
            }
        }

        return $branch;
    }

    /**
     * Récupère les catégories racines (sans parent)
     * 
     * @return array Liste des catégories racines
     */
    public function getRootCategories(): array
    {
        return $this->where('parent_id', null)
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    /**
     * Récupère les sous-catégories d'une catégorie
     * 
     * @param string $parentId ID de la catégorie parente
     * @return array Liste des sous-catégories
     */
    public function getChildCategories(string $parentId): array
    {
        return $this->where('parent_id', $parentId)
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    /**
     * Récupère le fil d'Ariane pour une catégorie
     * 
     * @param string $categoryId ID de la catégorie
     * @return array Chemin de la catégorie (du parent racine jusqu'à la catégorie)
     */
    public function getBreadcrumb(string $categoryId): array
    {
        $breadcrumb = [];
        $category = $this->find($categoryId);

        while ($category) {
            array_unshift($breadcrumb, $category);
            if ($category['parent_id']) {
                $category = $this->find($category['parent_id']);
            } else {
                $category = null;
            }
        }

        return $breadcrumb;
    }

    /**
     * Récupère toutes les catégories formatées pour un select HTML
     * 
     * @param string|null $companyId ID de l'entreprise
     * @return array Tableau associatif [id => nom avec indentation]
     */
    public function getForSelect(?string $companyId = null): array
    {
        $tree = $this->getCategoryTree($companyId);
        $options = [];
        $this->flattenTreeForSelect($tree, $options);
        return $options;
    }

    /**
     * Récupère une catégorie pour une entreprise spécifique
     * 
     * @param string $id ID de la catégorie
     * @param string $companyId ID de l'entreprise
     * @return array|null
     */
    public function findForCompany(string $id, string $companyId): ?array
    {
        return $this->where('id', $id)
                    ->where('company_id', $companyId)
                    ->first();
    }

    /**
     * Convertit l'arborescence en liste plate pour un select
     * 
     * @param array $tree Arborescence
     * @param array &$options Tableau de sortie
     * @param int $depth Niveau de profondeur
     */
    private function flattenTreeForSelect(array $tree, array &$options, int $depth = 0): void
    {
        foreach ($tree as $category) {
            $prefix = str_repeat('—', $depth) . ($depth > 0 ? ' ' : '');
            $options[$category['id']] = $prefix . $category['name'];

            if (isset($category['children'])) {
                $this->flattenTreeForSelect($category['children'], $options, $depth + 1);
            }
        }
    }

    /**
     * Vérifie si une catégorie a des produits associés
     * 
     * @param string $categoryId ID de la catégorie
     * @return bool
     */
    public function hasProducts(string $categoryId): bool
    {
        $db = \Config\Database::connect();
        $builder = $db->table('products');
        $count = $builder->where('category_id', $categoryId)->countAllResults();
        return $count > 0;
    }
}
