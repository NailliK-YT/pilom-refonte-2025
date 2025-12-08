<?php

namespace App\Models;

use CodeIgniter\Model;

class DepenseModel extends Model
{
    protected $table = 'depenses';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    protected $allowedFields = [
        'id',
        'company_id',
        'user_id',
        'date',
        'montant_ht',
        'montant_ttc',
        'tva_id',
        'description',
        'categorie_id',
        'fournisseur_id',
        'justificatif_path',
        'statut',
        'recurrent',
        'frequence_id',
        'methode_paiement'
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
        'user_id' => 'required',
        'date' => 'required|valid_date',
        'montant_ht' => 'required|decimal|greater_than_equal_to[0]',
        'montant_ttc' => 'required|decimal|greater_than_equal_to[0]',
        'tva_id' => 'required',
        'description' => 'required|min_length[3]',
        'categorie_id' => 'required',
        'statut' => 'required|in_list[brouillon,valide,archive]',
        'methode_paiement' => 'required|in_list[especes,cheque,virement,cb]'
    ];
    protected $validationMessages = [
        'date' => [
            'required' => 'La date est obligatoire',
            'valid_date' => 'La date n\'est pas valide'
        ],
        'montant_ht' => [
            'required' => 'Le montant HT est obligatoire',
            'greater_than_equal_to' => 'Le montant HT doit être positif'
        ],
        'description' => [
            'required' => 'La description est obligatoire',
            'min_length' => 'La description doit contenir au moins 3 caractères'
        ]
    ];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateId'];
    protected $beforeUpdate = ['logChanges'];

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
     * Log changes to historique_depenses before update
     */
    protected function logChanges(array $data)
    {
        if (!isset($data['id']) || !is_array($data['id'])) {
            return $data;
        }

        $depenseId = is_array($data['id']) ? $data['id'][0] : $data['id'];
        $oldData = $this->find($depenseId);

        if (!$oldData) {
            return $data;
        }

        $historiqueModel = new HistoriqueDepenseModel();
        $userId = session()->get('user_id');

        foreach ($data['data'] as $field => $newValue) {
            if (isset($oldData[$field]) && $oldData[$field] != $newValue) {
                $historiqueModel->log(
                    $depenseId,
                    $field,
                    $oldData[$field],
                    $newValue,
                    $userId
                );
            }
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
     * Calculate TTC from HT and TVA rate
     * 
     * @param float $montantHT Amount excluding tax
     * @param float $tvaRate TVA rate (percentage)
     * @return float
     */
    public function calculateTTC(float $montantHT, float $tvaRate): float
    {
        return round($montantHT * (1 + $tvaRate / 100), 2);
    }

    /**
     * Calculate TVA amount
     * 
     * @param float $montantHT Amount excluding tax
     * @param float $tvaRate TVA rate (percentage)
     * @return float
     */
    public function calculateTVA(float $montantHT, float $tvaRate): float
    {
        return round($montantHT * ($tvaRate / 100), 2);
    }

    /**
     * Search expenses with advanced filters
     * 
     * @param array $params Search parameters
     * @param int $limit Results per page
     * @param int $offset Offset for pagination
     * @return array
     */
    public function searchDepenses(array $params, int $limit = 20, int $offset = 0): array
    {
        $builder = $this->builder();

        $builder->select('depenses.*, 
                categories_depenses.nom as categorie_nom, 
                categories_depenses.couleur as categorie_couleur,
                fournisseurs.nom as fournisseur_nom,
                tva_rates.rate as tva_rate,
                users.email as user_email')
            ->join('categories_depenses', 'categories_depenses.id = depenses.categorie_id', 'left')
            ->join('fournisseurs', 'fournisseurs.id = depenses.fournisseur_id AND fournisseurs.deleted_at IS NULL', 'left')
            ->join('tva_rates', 'tva_rates.id = depenses.tva_id', 'left')
            ->join('users', 'users.id = depenses.user_id', 'left');

        // Filter by company
        if (!empty($params['company_id'])) {
            $builder->where('depenses.company_id', $params['company_id']);
        }

        // Filter by keywords
        if (!empty($params['keywords'])) {
            $keywords = $params['keywords'];
            $builder->groupStart()
                ->like('depenses.description', $keywords)
                ->orLike('fournisseurs.nom', $keywords)
                ->groupEnd();
        }

        // Filter by category
        if (!empty($params['category_id'])) {
            $builder->where('depenses.categorie_id', $params['category_id']);
        }

        // Filter by supplier
        if (!empty($params['fournisseur_id'])) {
            $builder->where('depenses.fournisseur_id', $params['fournisseur_id']);
        }

        // Filter by status
        if (!empty($params['statut'])) {
            $builder->where('depenses.statut', $params['statut']);
        }

        // Filter by date range
        if (!empty($params['date_debut'])) {
            $builder->where('depenses.date >=', $params['date_debut']);
        }
        if (!empty($params['date_fin'])) {
            $builder->where('depenses.date <=', $params['date_fin']);
        }

        // Filter by amount range
        if (!empty($params['min_montant'])) {
            $builder->where('depenses.montant_ttc >=', $params['min_montant']);
        }
        if (!empty($params['max_montant'])) {
            $builder->where('depenses.montant_ttc <=', $params['max_montant']);
        }

        // Filter by payment method
        if (!empty($params['methode_paiement'])) {
            $builder->where('depenses.methode_paiement', $params['methode_paiement']);
        }

        // Sort
        $sortBy = $params['sort_by'] ?? 'date';
        $sortOrder = $params['sort_order'] ?? 'DESC';
        $builder->orderBy('depenses.' . $sortBy, $sortOrder);

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

        $builder->join('fournisseurs', 'fournisseurs.id = depenses.fournisseur_id AND fournisseurs.deleted_at IS NULL', 'left');

        if (!empty($params['company_id'])) {
            $builder->where('depenses.company_id', $params['company_id']);
        }

        if (!empty($params['keywords'])) {
            $keywords = $params['keywords'];
            $builder->groupStart()
                ->like('depenses.description', $keywords)
                ->orLike('fournisseurs.nom', $keywords)
                ->groupEnd();
        }

        if (!empty($params['category_id'])) {
            $builder->where('depenses.categorie_id', $params['category_id']);
        }

        if (!empty($params['fournisseur_id'])) {
            $builder->where('depenses.fournisseur_id', $params['fournisseur_id']);
        }

        if (!empty($params['statut'])) {
            $builder->where('depenses.statut', $params['statut']);
        }

        if (!empty($params['date_debut'])) {
            $builder->where('depenses.date >=', $params['date_debut']);
        }
        if (!empty($params['date_fin'])) {
            $builder->where('depenses.date <=', $params['date_fin']);
        }

        if (!empty($params['min_montant'])) {
            $builder->where('depenses.montant_ttc >=', $params['min_montant']);
        }
        if (!empty($params['max_montant'])) {
            $builder->where('depenses.montant_ttc <=', $params['max_montant']);
        }

        if (!empty($params['methode_paiement'])) {
            $builder->where('depenses.methode_paiement', $params['methode_paiement']);
        }

        return $builder->countAllResults();
    }

    /**
     * Get expense with all relations
     * 
     * @param string $id Expense ID
     * @return array|null
     */
    public function getDepenseWithRelations(string $id): ?array
    {
        $builder = $this->builder();

        $result = $builder->select('depenses.*, 
                categories_depenses.nom as categorie_nom,
                categories_depenses.couleur as categorie_couleur,
                fournisseurs.nom as fournisseur_nom,
                fournisseurs.email as fournisseur_email,
                fournisseurs.telephone as fournisseur_telephone,
                tva_rates.rate as tva_rate,
                tva_rates.label as tva_label,
                users.email as user_email,
                frequences.nom as frequence_nom')
            ->join('categories_depenses', 'categories_depenses.id = depenses.categorie_id', 'left')
            ->join('fournisseurs', 'fournisseurs.id = depenses.fournisseur_id AND fournisseurs.deleted_at IS NULL', 'left')
            ->join('tva_rates', 'tva_rates.id = depenses.tva_id', 'left')
            ->join('users', 'users.id = depenses.user_id', 'left')
            ->join('frequences', 'frequences.id = depenses.frequence_id', 'left')
            ->where('depenses.id', $id)
            ->get()
            ->getRowArray();

        return $result ?: null;
    }

    /**
     * Get statistics by category for a period
     * 
     * @param string $companyId Company ID
     * @param string $startDate Start date (YYYY-MM-DD)
     * @param string $endDate End date (YYYY-MM-DD)
     * @return array
     */
    public function getStatsByCategory(string $companyId, string $startDate, string $endDate): array
    {
        $builder = $this->builder();

        return $builder->select('categories_depenses.nom as categorie, 
                categories_depenses.couleur as couleur,
                COUNT(depenses.id) as count,
                COALESCE(SUM(depenses.montant_ttc), 0) as total')
            ->join('categories_depenses', 'categories_depenses.id = depenses.categorie_id', 'left')
            ->where('depenses.company_id', $companyId)
            ->where('depenses.date >=', $startDate)
            ->where('depenses.date <=', $endDate)
            ->where('depenses.statut !=', 'brouillon')
            ->groupBy('depenses.categorie_id, categories_depenses.nom, categories_depenses.couleur')
            ->orderBy('total', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get statistics by period
     * 
     * @param string $companyId Company ID
     * @param string $period Period type: 'month', 'quarter', 'year'
     * @param int $count Number of periods to return
     * @return array
     */
    public function getStatsByPeriod(string $companyId, string $period = 'month', int $count = 12): array
    {
        $results = [];
        $currentDate = new \DateTime();

        for ($i = 0; $i < $count; $i++) {
            $periodData = $this->getPeriodDates($currentDate, $period);

            $builder = $this->builder();
            $stats = $builder->select('COUNT(id) as count, COALESCE(SUM(montant_ttc), 0) as total')
                ->where('company_id', $companyId)
                ->where('date >=', $periodData['start'])
                ->where('date <=', $periodData['end'])
                ->where('statut !=', 'brouillon')
                ->get()
                ->getRowArray();

            $results[] = [
                'period' => $periodData['label'],
                'start' => $periodData['start'],
                'end' => $periodData['end'],
                'count' => $stats['count'] ?? 0,
                'total' => $stats['total'] ?? 0
            ];

            // Move to previous period
            $this->moveToPreviousPeriod($currentDate, $period);
        }

        return array_reverse($results);
    }

    /**
     * Get period dates helper
     */
    private function getPeriodDates(\DateTime $date, string $period): array
    {
        $result = [];

        switch ($period) {
            case 'month':
                $result['start'] = $date->format('Y-m-01');
                $result['end'] = $date->format('Y-m-t');
                $result['label'] = $date->format('M Y');
                break;

            case 'quarter':
                $month = (int) $date->format('m');
                $quarter = ceil($month / 3);
                $startMonth = ($quarter - 1) * 3 + 1;
                $result['start'] = $date->format('Y') . '-' . str_pad($startMonth, 2, '0', STR_PAD_LEFT) . '-01';
                $endDate = new \DateTime($result['start']);
                $endDate->add(new \DateInterval('P3M'));
                $endDate->sub(new \DateInterval('P1D'));
                $result['end'] = $endDate->format('Y-m-d');
                $result['label'] = 'Q' . $quarter . ' ' . $date->format('Y');
                break;

            case 'year':
                $result['start'] = $date->format('Y-01-01');
                $result['end'] = $date->format('Y-12-31');
                $result['label'] = $date->format('Y');
                break;
        }

        return $result;
    }

    /**
     * Move date to previous period
     */
    private function moveToPreviousPeriod(\DateTime $date, string $period): void
    {
        switch ($period) {
            case 'month':
                $date->sub(new \DateInterval('P1M'));
                break;
            case 'quarter':
                $date->sub(new \DateInterval('P3M'));
                break;
            case 'year':
                $date->sub(new \DateInterval('P1Y'));
                break;
        }
    }

    /**
     * Get top suppliers by expense amount
     * 
     * @param string $companyId Company ID
     * @param int $limit Number of suppliers to return
     * @param string|null $startDate Optional start date
     * @param string|null $endDate Optional end date
     * @return array
     */
    public function getTopSuppliers(string $companyId, int $limit = 5, ?string $startDate = null, ?string $endDate = null): array
    {
        $builder = $this->builder();

        $builder->select('fournisseurs.nom as fournisseur, 
                COUNT(depenses.id) as count,
                COALESCE(SUM(depenses.montant_ttc), 0) as total')
            ->join('fournisseurs', 'fournisseurs.id = depenses.fournisseur_id AND fournisseurs.deleted_at IS NULL', 'inner')
            ->where('depenses.company_id', $companyId)
            ->where('depenses.statut !=', 'brouillon');

        if ($startDate) {
            $builder->where('depenses.date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('depenses.date <=', $endDate);
        }

        return $builder->groupBy('depenses.fournisseur_id, fournisseurs.nom')
            ->orderBy('total', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    /**
     * Archive an expense (soft delete)
     * 
     * @param string $id Expense ID
     * @return bool
     */
    public function archiveDepense(string $id): bool
    {
        return $this->update($id, ['statut' => 'archive']);
    }

    /**
     * Bulk archive expenses
     * 
     * @param array $ids Array of expense IDs
     * @return int Number of archived expenses
     */
    public function bulkArchive(array $ids): int
    {
        return $this->whereIn('id', $ids)->set(['statut' => 'archive'])->update();
    }

    /**
     * Get total expenses for dashboard
     * 
     * @param string $companyId Company ID
     * @param string $period 'month', 'year', or 'all'
     * @return float
     */
    public function getTotalExpenses(string $companyId, string $period = 'month'): float
    {
        $builder = $this->builder();
        $builder->where('company_id', $companyId)
            ->where('statut !=', 'brouillon');

        switch ($period) {
            case 'month':
                $builder->where('date >=', date('Y-m-01'));
                break;
            case 'year':
                $builder->where('date >=', date('Y-01-01'));
                break;
        }

        $result = $builder->select('COALESCE(SUM(montant_ttc), 0) as total')
            ->get()
            ->getRowArray();

        return (float) ($result['total'] ?? 0);
    }
}
