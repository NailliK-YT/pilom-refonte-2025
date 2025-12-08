<?php

namespace App\Models;

use CodeIgniter\Model;

class DepenseRecurrenceModel extends Model
{
    protected $table = 'depenses_recurrences';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'id',
        'depense_id',
        'date_debut',
        'date_fin',
        'prochaine_occurrence',
        'statut'
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'depense_id' => 'required',
        'date_debut' => 'required|valid_date',
        'date_fin' => 'permit_empty|valid_date',
        'prochaine_occurrence' => 'required|valid_date',
        'statut' => 'required|in_list[actif,suspendu,termine]'
    ];
    protected $validationMessages = [];
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
     * Get all active recurrences
     * 
     * @return array
     */
    public function getActiveRecurrences(): array
    {
        return $this->select('depenses_recurrences.*, 
                depenses.description,
                depenses.montant_ttc,
                depenses.categorie_id,
                categories_depenses.nom as categorie_nom,
                frequences.nom as frequence_nom,
                frequences.jours as frequence_jours')
            ->join('depenses', 'depenses.id = depenses_recurrences.depense_id', 'left')
            ->join('categories_depenses', 'categories_depenses.id = depenses.categorie_id', 'left')
            ->join('frequences', 'frequences.id = depenses.frequence_id', 'left')
            ->where('depenses_recurrences.statut', 'actif')
            ->orderBy('depenses_recurrences.prochaine_occurrence', 'ASC')
            ->findAll();
    }

    /**
     * Get recurrences due for generation
     * 
     * @param string $date Date to check (YYYY-MM-DD)
     * @return array
     */
    public function getDueRecurrences(string $date): array
    {
        return $this->select('depenses_recurrences.*, 
                depenses.*,
                depenses.id as depense_id,
                depenses_recurrences.id as recurrence_id,
                frequences.jours as frequence_jours')
            ->join('depenses', 'depenses.id = depenses_recurrences.depense_id', 'left')
            ->join('frequences', 'frequences.id = depenses.frequence_id', 'left')
            ->where('depenses_recurrences.statut', 'actif')
            ->where('depenses_recurrences.prochaine_occurrence <=', $date)
            ->findAll();
    }

    /**
     * Generate expense occurrences for a specific date
     * 
     * @param string $date Date to generate for (YYYY-MM-DD)
     * @return array Result with success/error counts
     */
    public function generateOccurrences(string $date): array
    {
        $dueRecurrences = $this->getDueRecurrences($date);
        $generated = 0;
        $errors = 0;
        $errorMessages = [];

        $depenseModel = new DepenseModel();

        foreach ($dueRecurrences as $recurrence) {
            try {
                // Create new expense from template
                $newExpenseData = [
                    'company_id' => $recurrence['company_id'],
                    'user_id' => $recurrence['user_id'],
                    'date' => $recurrence['prochaine_occurrence'],
                    'montant_ht' => $recurrence['montant_ht'],
                    'montant_ttc' => $recurrence['montant_ttc'],
                    'tva_id' => $recurrence['tva_id'],
                    'description' => $recurrence['description'] . ' (Récurrence auto)',
                    'categorie_id' => $recurrence['categorie_id'],
                    'fournisseur_id' => $recurrence['fournisseur_id'],
                    'statut' => 'valide',
                    'recurrent' => false,
                    'methode_paiement' => $recurrence['methode_paiement']
                ];

                if ($depenseModel->insert($newExpenseData)) {
                    // Calculate next occurrence
                    $nextOccurrence = $this->calculateNextOccurrence(
                        $recurrence['prochaine_occurrence'],
                        $recurrence['frequence_jours']
                    );

                    // Check if we should terminate (date_fin reached)
                    $shouldTerminate = false;
                    if ($recurrence['date_fin'] && $nextOccurrence > $recurrence['date_fin']) {
                        $shouldTerminate = true;
                    }

                    // Update recurrence
                    $this->update($recurrence['recurrence_id'], [
                        'prochaine_occurrence' => $nextOccurrence,
                        'statut' => $shouldTerminate ? 'termine' : 'actif'
                    ]);

                    $generated++;
                } else {
                    $errors++;
                    $errorMessages[] = "Erreur pour récurrence ID " . $recurrence['recurrence_id'];
                }
            } catch (\Exception $e) {
                $errors++;
                $errorMessages[] = "Exception pour récurrence ID " . $recurrence['recurrence_id'] . ": " . $e->getMessage();
                log_message('error', 'Erreur génération récurrence: ' . $e->getMessage());
            }
        }

        return [
            'generated' => $generated,
            'errors' => $errors,
            'errorMessages' => $errorMessages
        ];
    }

    /**
     * Calculate next occurrence date
     * 
     * @param string $currentDate Current date (YYYY-MM-DD)
     * @param int $days Number of days to add
     * @return string Next occurrence date (YYYY-MM-DD)
     */
    private function calculateNextOccurrence(string $currentDate, int $days): string
    {
        $date = new \DateTime($currentDate);
        $date->add(new \DateInterval('P' . $days . 'D'));
        return $date->format('Y-m-d');
    }

    /**
     * Update next occurrence date
     * 
     * @param string $id Recurrence ID
     * @param string $nextDate Next occurrence date
     * @return bool
     */
    public function updateNextOccurrence(string $id, string $nextDate): bool
    {
        return $this->update($id, ['prochaine_occurrence' => $nextDate]);
    }

    /**
     * Suspend a recurrence
     * 
     * @param string $id Recurrence ID
     * @return bool
     */
    public function suspend(string $id): bool
    {
        return $this->update($id, ['statut' => 'suspendu']);
    }

    /**
     * Resume a suspended recurrence
     * 
     * @param string $id Recurrence ID
     * @return bool
     */
    public function resume(string $id): bool
    {
        return $this->update($id, ['statut' => 'actif']);
    }

    /**
     * Terminate a recurrence
     * 
     * @param string $id Recurrence ID
     * @return bool
     */
    public function terminate(string $id): bool
    {
        return $this->update($id, ['statut' => 'termine']);
    }

    /**
     * Get recurrence by expense ID
     * 
     * @param string $depenseId Expense ID
     * @return array|null
     */
    public function getByDepenseId(string $depenseId): ?array
    {
        return $this->where('depense_id', $depenseId)->first();
    }

    /**
     * Get generated expenses from a recurring expense
     * 
     * @param string $depenseId Template expense ID
     * @param int $limit Number of records to retrieve
     * @return array
     */
    public function getGeneratedExpenses(string $depenseId, int $limit = 50): array
    {
        // Get the template expense
        $depenseModel = new DepenseModel();
        $template = $depenseModel->find($depenseId);

        if (!$template) {
            return [];
        }

        // Find generated expenses (similar description, same category/supplier)
        return $depenseModel->where('categorie_id', $template['categorie_id'])
            ->where('fournisseur_id', $template['fournisseur_id'])
            ->where('recurrent', false)
            ->like('description', '(Récurrence auto)')
            ->orderBy('date', 'DESC')
            ->limit($limit)
            ->findAll();
    }
}
