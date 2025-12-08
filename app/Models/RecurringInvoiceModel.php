<?php

namespace App\Models;

use CodeIgniter\Model;

class RecurringInvoiceModel extends Model
{
    protected $table      = 'recurring_invoices';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'contact_id',
        'frequency',
        'start_date',
        'end_date',
        'next_run_date',
        'montant_ht',
        'montant_tva',
        'montant_ttc',
        'status'
    ];

    /**
     * Récupère les factures récurrentes actives dues pour une date donnée
     * 
     * @param string $date Date de référence (généralement aujourd'hui)
     * @return array
     */
    public function getDueInvoices(string $date): array
    {
        return $this->where('status', 'active')
                    ->where('next_run_date <=', $date)
                    ->where('(end_date IS NULL OR end_date >= next_run_date)')
                    ->findAll();
    }
}
