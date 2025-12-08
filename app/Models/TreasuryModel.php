<?php

namespace App\Models;

use CodeIgniter\Model;

class TreasuryModel extends Model
{
    protected $table = 'treasury_entries';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'id',
        'company_id',
        'type',
        'category',
        'amount',
        'balance_after',
        'reference_type',
        'reference_id',
        'description',
        'transaction_date',
        'created_by'
    ];

    protected bool $allowEmptyInserts = false;
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateId', 'calculateBalance'];
    
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
     * Calculate balance after transaction
     */
    protected function calculateBalance(array $data)
    {
        if (isset($data['data']['company_id']) && isset($data['data']['amount'])) {
            $currentBalance = $this->getCurrentBalance($data['data']['company_id']);
            $amount = (float) $data['data']['amount'];
            
            if ($data['data']['type'] === 'exit') {
                $data['data']['balance_after'] = $currentBalance - abs($amount);
            } else {
                $data['data']['balance_after'] = $currentBalance + abs($amount);
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
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    /**
     * Get current balance for a company
     */
    public function getCurrentBalance(string $companyId): float
    {
        $lastEntry = $this->where('company_id', $companyId)
            ->orderBy('transaction_date', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->first();
        
        return $lastEntry ? (float) $lastEntry['balance_after'] : 0.0;
    }

    /**
     * Get entries for a company with optional filters
     */
    public function getEntriesForCompany(string $companyId, array $filters = []): array
    {
        $builder = $this->where('company_id', $companyId);
        
        if (!empty($filters['type'])) {
            $builder->where('type', $filters['type']);
        }
        
        if (!empty($filters['category'])) {
            $builder->where('category', $filters['category']);
        }
        
        if (!empty($filters['date_from'])) {
            $builder->where('transaction_date >=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $builder->where('transaction_date <=', $filters['date_to']);
        }
        
        return $builder->orderBy('transaction_date', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get summary statistics for a company
     */
    public function getSummary(string $companyId, ?string $startDate = null, ?string $endDate = null): array
    {
        $builder = $this->where('company_id', $companyId);
        
        if ($startDate) {
            $builder->where('transaction_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('transaction_date <=', $endDate);
        }
        
        $entries = $builder->findAll();
        
        $totalIn = 0;
        $totalOut = 0;
        
        foreach ($entries as $entry) {
            if ($entry['type'] === 'entry') {
                $totalIn += abs((float) $entry['amount']);
            } else {
                $totalOut += abs((float) $entry['amount']);
            }
        }
        
        return [
            'current_balance' => $this->getCurrentBalance($companyId),
            'total_entries' => $totalIn,
            'total_exits' => $totalOut,
            'net_flow' => $totalIn - $totalOut,
            'transaction_count' => count($entries)
        ];
    }

    /**
     * Get monthly data for charts
     */
    public function getMonthlyData(string $companyId, int $months = 12): array
    {
        $startDate = date('Y-m-01', strtotime("-{$months} months"));
        
        $entries = $this->where('company_id', $companyId)
            ->where('transaction_date >=', $startDate)
            ->orderBy('transaction_date', 'ASC')
            ->findAll();
        
        $monthlyData = [];
        
        foreach ($entries as $entry) {
            $month = date('Y-m', strtotime($entry['transaction_date']));
            
            if (!isset($monthlyData[$month])) {
                $monthlyData[$month] = ['entries' => 0, 'exits' => 0];
            }
            
            if ($entry['type'] === 'entry') {
                $monthlyData[$month]['entries'] += abs((float) $entry['amount']);
            } else {
                $monthlyData[$month]['exits'] += abs((float) $entry['amount']);
            }
        }
        
        return $monthlyData;
    }

    /**
     * Add entry from invoice payment
     */
    public function addFromInvoice(string $companyId, string $factureId, float $amount, string $description = ''): bool
    {
        return $this->insert([
            'company_id' => $companyId,
            'type' => 'entry',
            'category' => 'invoice',
            'amount' => $amount,
            'reference_type' => 'facture',
            'reference_id' => $factureId,
            'description' => $description ?: 'Paiement facture',
            'transaction_date' => date('Y-m-d'),
            'created_by' => session()->get('user_id')
        ]) !== false;
    }

    /**
     * Add exit from expense
     */
    public function addFromExpense(string $companyId, string $depenseId, float $amount, string $description = ''): bool
    {
        return $this->insert([
            'company_id' => $companyId,
            'type' => 'exit',
            'category' => 'expense',
            'amount' => $amount,
            'reference_type' => 'depense',
            'reference_id' => $depenseId,
            'description' => $description ?: 'Dépense',
            'transaction_date' => date('Y-m-d'),
            'created_by' => session()->get('user_id')
        ]) !== false;
    }
}
