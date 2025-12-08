<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Modèle pour la gestion des paliers de prix dégressifs
 * Permet de définir des prix différents selon la quantité commandée
 */
class PriceTierModel extends Model
{
    protected $table = 'price_tiers';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'id',
        'product_id',
        'min_quantity',
        'price_ht'
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'product_id' => 'required|is_not_unique[products.id]',
        'min_quantity' => 'required|integer|greater_than_equal_to[1]',
        'price_ht' => 'required|decimal|greater_than_equal_to[0]'
    ];

    protected $validationMessages = [
        'product_id' => [
            'required' => 'Le produit est obligatoire',
            'is_not_unique' => 'Le produit sélectionné n\'existe pas'
        ],
        'min_quantity' => [
            'required' => 'La quantité minimum est obligatoire',
            'integer' => 'La quantité doit être un nombre entier',
            'greater_than_equal_to' => 'La quantité minimum doit être au moins 1'
        ],
        'price_ht' => [
            'required' => 'Le prix HT est obligatoire',
            'decimal' => 'Le prix doit être un nombre décimal',
            'greater_than_equal_to' => 'Le prix doit être supérieur ou égal à 0'
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
     * Récupère tous les paliers de prix d'un produit
     * Triés par quantité minimum croissante
     * 
     * @param string $productId ID du produit
     * @return array Liste des paliers de prix
     */
    public function getPriceTiersByProduct(string $productId): array
    {
        return $this->where('product_id', $productId)
            ->orderBy('min_quantity', 'ASC')
            ->findAll();
    }

    /**
     * Supprime tous les paliers de prix d'un produit
     * 
     * @param string $productId ID du produit
     * @return bool
     */
    public function deleteByProduct(string $productId): bool
    {
        return $this->where('product_id', $productId)->delete();
    }

    /**
     * Obtenir le prix applicable en fonction de la quantité
     * 
     * @param string $productId ID du produit
     * @param int $quantity Quantité commandée
     * @return float|null Prix applicable ou null si pas de palier trouvé
     */
    public function getPriceForQuantity(string $productId, int $quantity): ?float
    {
        $tiers = $this->getPriceTiersByProduct($productId);

        $applicablePrice = null;
        foreach ($tiers as $tier) {
            if ($quantity >= $tier['min_quantity']) {
                $applicablePrice = $tier['price_ht'];
            } else {
                break; // Les paliers sont triés, on peut arrêter
            }
        }

        return $applicablePrice;
    }
}
