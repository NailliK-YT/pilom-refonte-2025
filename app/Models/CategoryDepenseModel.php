<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryDepenseModel extends Model
{
    protected $table = 'categories_depenses';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'id',
        'nom',
        'couleur',
        'description',
        'user_id'
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'nom' => 'required|min_length[3]|max_length[100]',
        'couleur' => 'required|regex_match[/^#[0-9A-Fa-f]{6}$/]',
        'description' => 'permit_empty|max_length[500]'
    ];
    protected $validationMessages = [
        'nom' => [
            'required' => 'Le nom de la catégorie est obligatoire',
            'min_length' => 'Le nom doit contenir au moins 3 caractères'
        ],
        'couleur' => [
            'required' => 'La couleur est obligatoire',
            'regex_match' => 'La couleur doit être au format hexadécimal (#RRGGBB)'
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
     * Get all categories formatted for select dropdown
     * 
     * @param string|null $userId Optional user ID to include custom categories
     * @return array Associative array with id as key and name as value
     */
    public function getForSelect(?string $userId = null): array
    {
        $builder = $this->builder();

        // Get predefined categories (user_id is NULL)
        $builder->where('user_id IS NULL', null, false);

        // Add user's custom categories if userId provided
        if ($userId) {
            $builder->orWhere('user_id', $userId);
        }

        $builder->orderBy('nom', 'ASC');
        $categories = $builder->get()->getResultArray();

        $result = [];
        foreach ($categories as $cat) {
            $result[$cat['id']] = $cat['nom'];
        }

        return $result;
    }

    /**
     * Get all predefined system categories
     * 
     * @return array
     */
    public function getPredefinedCategories(): array
    {
        return $this->where('user_id IS NULL', null, false)
            ->orderBy('nom', 'ASC')
            ->findAll();
    }

    /**
     * Get user's custom categories
     * 
     * @param string $userId User ID
     * @return array
     */
    public function getUserCategories(string $userId): array
    {
        return $this->where('user_id', $userId)
            ->orderBy('nom', 'ASC')
            ->findAll();
    }

    /**
     * Get all categories with expense count
     * 
     * @param string|null $userId Optional user ID
     * @return array
     */
    public function getWithStats(?string $userId = null): array
    {
        $builder = $this->builder();

        $builder->select('categories_depenses.*, COUNT(depenses.id) as expense_count, COALESCE(SUM(depenses.montant_ttc), 0) as total_montant')
            ->join('depenses', 'depenses.categorie_id = categories_depenses.id', 'left')
            ->where('depenses.deleted_at IS NULL', null, false)
            ->groupBy('categories_depenses.id');

        if ($userId) {
            $builder->where('(categories_depenses.user_id IS NULL OR categories_depenses.user_id = ' . $this->db->escape($userId) . ')', null, false);
        } else {
            $builder->where('categories_depenses.user_id IS NULL', null, false);
        }

        $builder->orderBy('categories_depenses.nom', 'ASC');

        return $builder->get()->getResultArray();
    }

    /**
     * Check if category is predefined (system category)
     * 
     * @param string $id Category ID
     * @return bool
     */
    public function isPredefined(string $id): bool
    {
        $category = $this->find($id);
        return $category && $category['user_id'] === null;
    }
}
