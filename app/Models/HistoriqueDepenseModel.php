<?php

namespace App\Models;

use CodeIgniter\Model;

class HistoriqueDepenseModel extends Model
{
    protected $table = 'historique_depenses';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'id',
        'depense_id',
        'champ_modifie',
        'ancienne_valeur',
        'nouvelle_valeur',
        'modifie_par',
        'date_modification'
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = false; // We manually set date_modification
    protected $dateFormat = 'datetime';

    // Validation
    protected $validationRules = [
        'depense_id' => 'required',
        'champ_modifie' => 'required|max_length[100]',
        'modifie_par' => 'required'
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateId', 'setModificationDate'];
    protected $beforeUpdate = []; // No updates allowed on history

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
     * Set modification date
     */
    protected function setModificationDate(array $data)
    {
        if (!isset($data['data']['date_modification'])) {
            $data['data']['date_modification'] = date('Y-m-d H:i:s');
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
     * Log a change to the history
     * 
     * @param string $depenseId Expense ID
     * @param string $champ Field that was modified
     * @param mixed $ancienneValeur Old value
     * @param mixed $nouvelleValeur New value
     * @param string $modifiePar User ID who made the change
     * @return bool|string Insert ID or false
     */
    public function log(
        string $depenseId,
        string $champ,
        $ancienneValeur,
        $nouvelleValeur,
        string $modifiePar
    ) {
        // Convert arrays/objects to JSON
        if (is_array($ancienneValeur) || is_object($ancienneValeur)) {
            $ancienneValeur = json_encode($ancienneValeur);
        }
        if (is_array($nouvelleValeur) || is_object($nouvelleValeur)) {
            $nouvelleValeur = json_encode($nouvelleValeur);
        }

        return $this->insert([
            'depense_id' => $depenseId,
            'champ_modifie' => $champ,
            'ancienne_valeur' => $ancienneValeur,
            'nouvelle_valeur' => $nouvelleValeur,
            'modifie_par' => $modifiePar
        ], true);
    }

    /**
     * Get history for an expense with user information
     * 
     * @param string $depenseId Expense ID
     * @return array
     */
    public function getHistory(string $depenseId): array
    {
        return $this->select('historique_depenses.*, users.email as modifie_par_email')
            ->join('users', 'users.id = historique_depenses.modifie_par', 'left')
            ->where('depense_id', $depenseId)
            ->orderBy('date_modification', 'DESC')
            ->findAll();
    }

    /**
     * Get formatted history with human-readable field names
     * 
     * @param string $depenseId Expense ID
     * @return array
     */
    public function getFormattedHistory(string $depenseId): array
    {
        $history = $this->getHistory($depenseId);

        $fieldLabels = [
            'date' => 'Date',
            'montant_ht' => 'Montant HT',
            'montant_ttc' => 'Montant TTC',
            'tva_id' => 'TVA',
            'description' => 'Description',
            'categorie_id' => 'Catégorie',
            'fournisseur_id' => 'Fournisseur',
            'justificatif_path' => 'Justificatif',
            'statut' => 'Statut',
            'methode_paiement' => 'Méthode de paiement',
            'recurrent' => 'Récurrent',
            'frequence_id' => 'Fréquence'
        ];

        foreach ($history as &$entry) {
            $entry['champ_libelle'] = $fieldLabels[$entry['champ_modifie']] ?? $entry['champ_modifie'];

            // Format values based on field type
            $entry['ancienne_valeur_formatted'] = $this->formatValue(
                $entry['champ_modifie'],
                $entry['ancienne_valeur']
            );
            $entry['nouvelle_valeur_formatted'] = $this->formatValue(
                $entry['champ_modifie'],
                $entry['nouvelle_valeur']
            );
        }

        return $history;
    }

    /**
     * Format value for display
     * 
     * @param string $field Field name
     * @param mixed $value Value to format
     * @return string
     */
    private function formatValue(string $field, $value): string
    {
        if ($value === null || $value === '') {
            return '(vide)';
        }

        switch ($field) {
            case 'montant_ht':
            case 'montant_ttc':
                return number_format((float) $value, 2, ',', ' ') . ' €';

            case 'date':
                return date('d/m/Y', strtotime($value));

            case 'statut':
                $statuts = [
                    'brouillon' => 'Brouillon',
                    'valide' => 'Validé',
                    'archive' => 'Archivé'
                ];
                return $statuts[$value] ?? $value;

            case 'methode_paiement':
                $methodes = [
                    'especes' => 'Espèces',
                    'cheque' => 'Chèque',
                    'virement' => 'Virement',
                    'cb' => 'Carte bancaire'
                ];
                return $methodes[$value] ?? $value;

            case 'recurrent':
                return $value ? 'Oui' : 'Non';

            default:
                return (string) $value;
        }
    }

    /**
     * Get change count for an expense
     * 
     * @param string $depenseId Expense ID
     * @return int
     */
    public function getChangeCount(string $depenseId): int
    {
        return $this->where('depense_id', $depenseId)->countAllResults();
    }

    /**
     * Prevent updates and deletes on history records
     */
    public function update($id = null, $data = null): bool
    {
        log_message('warning', 'Attempted to update history record - operation blocked');
        return false;
    }

    /**
     * Prevent deletes on history records
     */
    public function delete($id = null, bool $purge = false)
    {
        log_message('warning', 'Attempted to delete history record - operation blocked');
        return false;
    }
}
