<?php

namespace App\Models;

use CodeIgniter\Model;

class FournisseurModel extends Model
{
    protected $table = 'fournisseurs';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    protected $allowedFields = [
        'id',
        'company_id',
        'nom',
        'adresse',
        'contact',
        'email',
        'telephone',
        'siret'
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'company_id' => 'required',
        'nom' => 'required|min_length[2]|max_length[255]',
        'adresse' => 'permit_empty|max_length[1000]',
        'contact' => 'permit_empty|max_length[255]',
        'email' => 'permit_empty|valid_email',
        'telephone' => 'permit_empty|max_length[20]',
        'siret' => 'permit_empty|exact_length[14]|numeric'
    ];
    protected $validationMessages = [
        'nom' => [
            'required' => 'Le nom du fournisseur est obligatoire'
        ],
        'email' => [
            'valid_email' => 'L\'email n\'est pas valide'
        ],
        'siret' => [
            'exact_length' => 'Le SIRET doit contenir exactement 14 chiffres',
            'numeric' => 'Le SIRET ne doit contenir que des chiffres'
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
     * Search suppliers with filters
     * 
     * @param array $params Search parameters
     * @param int $limit Results per page
     * @param int $offset Offset for pagination
     * @return array
     */
    public function searchFournisseurs(array $params, int $limit = 20, int $offset = 0): array
    {
        $builder = $this->builder();

        // Filter by company
        if (!empty($params['company_id'])) {
            $builder->where('company_id', $params['company_id']);
        }

        // Search by keywords (nom, contact, email, siret)
        if (!empty($params['keywords'])) {
            $keywords = $params['keywords'];
            $builder->groupStart()
                ->like('nom', $keywords)
                ->orLike('contact', $keywords)
                ->orLike('email', $keywords)
                ->orLike('siret', $keywords)
                ->groupEnd();
        }

        // Sort
        $sortBy = $params['sort_by'] ?? 'nom';
        $sortOrder = $params['sort_order'] ?? 'ASC';
        $builder->orderBy($sortBy, $sortOrder);

        return $builder->limit($limit, $offset)->get()->getResultArray();
    }

    /**
     * Count search results
     * 
     * @param array $params Search parameters
     * @return int
     */
    public function countSearchResults(array $params): int
    {
        $builder = $this->builder();

        if (!empty($params['company_id'])) {
            $builder->where('company_id', $params['company_id']);
        }

        if (!empty($params['keywords'])) {
            $keywords = $params['keywords'];
            $builder->groupStart()
                ->like('nom', $keywords)
                ->orLike('contact', $keywords)
                ->orLike('email', $keywords)
                ->orLike('siret', $keywords)
                ->groupEnd();
        }

        return $builder->countAllResults();
    }

    /**
     * Get suppliers by company
     * 
     * @param string $companyId Company ID
     * @return array
     */
    public function getByCompany(string $companyId): array
    {
        return $this->where('company_id', $companyId)
            ->orderBy('nom', 'ASC')
            ->findAll();
    }

    /**
     * Get supplier with expense statistics
     * 
     * @param string $id Supplier ID
     * @return array|null
     */
    public function getWithStats(string $id): ?array
    {
        $builder = $this->builder();

        $result = $builder->select('fournisseurs.*, 
                COUNT(depenses.id) as expense_count, 
                COALESCE(SUM(depenses.montant_ttc), 0) as total_montant,
                MAX(depenses.date) as derniere_depense')
            ->join('depenses', 'depenses.fournisseur_id = fournisseurs.id AND depenses.deleted_at IS NULL', 'left')
            ->where('fournisseurs.id', $id)
            ->groupBy('fournisseurs.id')
            ->get()
            ->getRowArray();

        return $result ?: null;
    }

    /**
     * Get total expenses for a supplier
     * 
     * @param string $fournisseurId Supplier ID
     * @return float
     */
    public function getTotalDepenses(string $fournisseurId): float
    {
        $builder = $this->db->table('depenses');

        $result = $builder->select('COALESCE(SUM(montant_ttc), 0) as total')
            ->where('fournisseur_id', $fournisseurId)
            ->where('deleted_at IS NULL', null, false)
            ->get()
            ->getRowArray();

        return (float) ($result['total'] ?? 0);
    }

    /**
     * Get suppliers for select dropdown
     * 
     * @param string $companyId Company ID
     * @return array
     */
    public function getForSelect(string $companyId): array
    {
        $fournisseurs = $this->where('company_id', $companyId)
            ->orderBy('nom', 'ASC')
            ->findAll();

        $result = [];
        foreach ($fournisseurs as $fournisseur) {
            $result[$fournisseur['id']] = $fournisseur['nom'];
        }

        return $result;
    }

    /**
     * Import suppliers from CSV data
     * 
     * @param array $data CSV data rows
     * @param string $companyId Company ID
     * @return array Result with success/error counts
     */
    public function importFromCSV(array $data, string $companyId): array
    {
        $imported = 0;
        $errors = 0;
        $errorMessages = [];

        foreach ($data as $index => $row) {
            try {
                $supplierData = [
                    'company_id' => $companyId,
                    'nom' => $row['nom'] ?? '',
                    'adresse' => $row['adresse'] ?? null,
                    'contact' => $row['contact'] ?? null,
                    'email' => $row['email'] ?? null,
                    'telephone' => $row['telephone'] ?? null,
                    'siret' => $row['siret'] ?? null
                ];

                if ($this->insert($supplierData)) {
                    $imported++;
                } else {
                    $errors++;
                    $errorMessages[] = "Ligne " . ($index + 1) . ": " . implode(', ', $this->errors());
                }
            } catch (\Exception $e) {
                $errors++;
                $errorMessages[] = "Ligne " . ($index + 1) . ": " . $e->getMessage();
            }
        }

        return [
            'imported' => $imported,
            'errors' => $errors,
            'errorMessages' => $errorMessages
        ];
    }
}
