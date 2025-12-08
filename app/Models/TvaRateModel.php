<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Modèle pour la gestion des taux de TVA
 * Gère les différents taux de TVA applicables aux produits/services
 */
class TvaRateModel extends Model
{
    protected $table = 'tva_rates';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'id',
        'company_id',
        'rate',
        'label',
        'is_default'
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'rate' => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
        'label' => 'required|min_length[2]|max_length[100]',
        'is_default' => 'permit_empty|in_list[0,1,true,false]'
    ];

    protected $validationMessages = [
        'rate' => [
            'required' => 'Le taux de TVA est obligatoire',
            'decimal' => 'Le taux doit être un nombre décimal',
            'greater_than_equal_to' => 'Le taux doit être supérieur ou égal à 0',
            'less_than_equal_to' => 'Le taux doit être inférieur ou égal à 100'
        ],
        'label' => [
            'required' => 'Le libellé est obligatoire',
            'min_length' => 'Le libellé doit contenir au moins 2 caractères',
            'max_length' => 'Le libellé ne peut pas dépasser 100 caractères'
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
     * Récupère le taux de TVA par défaut pour une entreprise
     * 
     * @param string|null $companyId ID de l'entreprise
     * @return array|null Le taux par défaut ou null si non trouvé
     */
    public function getDefaultRate(?string $companyId = null)
    {
        $builder = $this->where('is_default', true);
        if ($companyId) {
            $builder->where('company_id', $companyId);
        }
        return $builder->first();
    }

    /**
     * Récupère tous les taux actifs triés par taux décroissant pour une entreprise
     * 
     * @param string|null $companyId ID de l'entreprise
     * @return array Liste des taux de TVA
     */
    public function getAllRates(?string $companyId = null)
    {
        $builder = $this;
        if ($companyId) {
            $builder = $builder->where('company_id', $companyId);
        }
        return $builder->orderBy('rate', 'DESC')->findAll();
    }

    /**
     * Récupère un taux de TVA pour une entreprise spécifique
     * 
     * @param string $id ID du taux
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
     * Alias pour getAllRates compatible avec le contrôleur
     */
    public function getActive()
    {
        return $this->getAllRates();
    }

    /**
     * Définit un taux comme taux par défaut pour une entreprise
     * Supprime le flag default des autres taux de cette entreprise
     * 
     * @param string $rateId ID du taux à définir par défaut
     * @param string $companyId ID de l'entreprise
     * @return bool
     */
    public function setAsDefault(string $rateId, string $companyId): bool
    {
        // Désactiver tous les taux par défaut pour cette entreprise
        $this->where('is_default', true)
             ->where('company_id', $companyId)
             ->set(['is_default' => false])
             ->update();

        // Activer le nouveau taux par défaut
        return $this->update($rateId, ['is_default' => true]);
    }

    /**
     * Calcule le prix TTC à partir d'un prix HT et d'un taux
     * 
     * @param float $priceHT Prix hors taxes
     * @param float $rate Taux de TVA
     * @return float Prix TTC
     */
    public function calculatePriceTTC(float $priceHT, float $rate): float
    {
        return round($priceHT * (1 + $rate / 100), 2);
    }
}
