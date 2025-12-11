<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Modèle principal pour la gestion des produits et services
 * Gère les relations avec TVA, catégories et prix dégressifs
 */
class ProductModel extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'id',
        'name',
        'description',
        'reference',
        'price_ht',
        'tva_id',
        'category_id',
        'company_id',
        'image_path',
        'is_archived'
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'name' => 'required|min_length[3]|max_length[255]',
        'description' => 'permit_empty|max_length[5000]',
        'reference' => 'required|alpha_numeric_punct|max_length[100]|is_unique[products.reference,id,{id}]',
        'price_ht' => 'required|decimal|greater_than_equal_to[0]',
        'tva_id' => 'required|is_not_unique[tva_rates.id]',
        'category_id' => 'required|is_not_unique[categories.id]',
        'image_path' => 'permit_empty|max_length[500]',
        'is_archived' => 'permit_empty|in_list[0,1,true,false]'
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'Le nom du produit est obligatoire',
            'min_length' => 'Le nom doit contenir au moins 3 caractères',
            'max_length' => 'Le nom ne peut pas dépasser 255 caractères'
        ],
        'reference' => [
            'required' => 'La référence est obligatoire',
            'alpha_numeric_punct' => 'La référence ne peut contenir que des lettres, chiffres et caractères de ponctuation',
            'is_unique' => 'Cette référence est déjà utilisée par un autre produit'
        ],
        'price_ht' => [
            'required' => 'Le prix HT est obligatoire',
            'decimal' => 'Le prix doit être un nombre décimal',
            'greater_than_equal_to' => 'Le prix doit être supérieur ou égal à 0'
        ],
        'tva_id' => [
            'required' => 'Le taux de TVA est obligatoire',
            'is_not_unique' => 'Le taux de TVA sélectionné n\'existe pas'
        ],
        'category_id' => [
            'required' => 'La catégorie est obligatoire',
            'is_not_unique' => 'La catégorie sélectionnée n\'existe pas'
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
     * Récupère les produits avec leurs relations (TVA et catégorie)
     * 
     * @param int $limit Nombre de résultats
     * @param int $offset Offset pour la pagination
     * @return array Liste des produits avec relations
     */
    public function getProductsWithRelations(int $limit = 20, int $offset = 0): array
    {
        return $this->select('products.*, tva_rates.rate as tva_rate, tva_rates.label as tva_label, categories.name as category_name')
            ->join('tva_rates', 'tva_rates.id = products.tva_id')
            ->join('categories', 'categories.id = products.category_id')
            ->where('products.is_archived', false)
            ->orderBy('products.created_at', 'DESC')
            ->findAll($limit, $offset);
    }

    /**
     * Récupère un produit avec toutes ses relations
     * 
     * @param string $productId ID du produit
     * @return array|null Produit avec relations ou null
     */
    public function getProductWithRelations(string $productId): ?array
    {
        $product = $this->select('products.*, tva_rates.rate as tva_rate, tva_rates.label as tva_label, categories.name as category_name')
            ->join('tva_rates', 'tva_rates.id = products.tva_id')
            ->join('categories', 'categories.id = products.category_id')
            ->find($productId);

        return $product ?: null;
    }

    /**
     * Recherche avancée de produits avec filtres
     * 
     * @param array $params Paramètres de recherche [keywords, category_id, min_price, max_price, is_archived]
     * @param int $limit Nombre de résultats
     * @param int $offset Offset pour la pagination
     * @return array Liste des produits correspondants
     */
    public function searchProducts(array $params, int $limit = 20, int $offset = 0): array
    {
        $builder = $this->select('products.*, tva_rates.rate as tva_rate, tva_rates.label as tva_label, categories.name as category_name')
            ->join('tva_rates', 'tva_rates.id = products.tva_id')
            ->join('categories', 'categories.id = products.category_id');

        // Filtre par entreprise (obligatoire pour la sécurité multi-tenant)
        if (!empty($params['company_id'])) {
            $builder->where('products.company_id', $params['company_id']);
        }

        // Recherche textuelle
        if (!empty($params['keywords'])) {
            $keywords = $params['keywords'];
            $builder->groupStart()
                ->like('products.name', $keywords)
                ->orLike('products.description', $keywords)
                ->orLike('products.reference', $keywords)
                ->groupEnd();
        }

        // Filtre par catégorie
        if (!empty($params['category_id'])) {
            $builder->where('products.category_id', $params['category_id']);
        }

        // Filtre par plage de prix
        if (isset($params['min_price']) && $params['min_price'] !== '') {
            $builder->where('products.price_ht >=', $params['min_price']);
        }
        if (isset($params['max_price']) && $params['max_price'] !== '') {
            $builder->where('products.price_ht <=', $params['max_price']);
        }

        // Filtre par statut (archivé ou non)
        if (isset($params['is_archived'])) {
            $builder->where('products.is_archived', $params['is_archived']);
        } else {
            $builder->where('products.is_archived', false); // Par défaut, masquer les archivés
        }

        // Tri
        $sortBy = $params['sort_by'] ?? 'created_at';
        $sortOrder = $params['sort_order'] ?? 'DESC';

        $allowedSorts = ['name', 'price_ht', 'created_at', 'reference'];
        if (in_array($sortBy, $allowedSorts)) {
            $builder->orderBy('products.' . $sortBy, $sortOrder);
        }

        return $builder->findAll($limit, $offset);
    }

    /**
     * Compte le nombre de résultats d'une recherche
     * 
     * @param array $params Paramètres de recherche
     * @return int Nombre de résultats
     */
    public function countSearchResults(array $params): int
    {
        $builder = $this->builder();

        // Filtre par entreprise (obligatoire pour la sécurité multi-tenant)
        if (!empty($params['company_id'])) {
            $builder->where('company_id', $params['company_id']);
        }

        // Recherche textuelle
        if (!empty($params['keywords'])) {
            $keywords = $params['keywords'];
            $builder->groupStart()
                ->like('name', $keywords)
                ->orLike('description', $keywords)
                ->orLike('reference', $keywords)
                ->groupEnd();
        }

        // Filtre par catégorie
        if (!empty($params['category_id'])) {
            $builder->where('category_id', $params['category_id']);
        }

        // Filtre par plage de prix
        if (isset($params['min_price']) && $params['min_price'] !== '') {
            $builder->where('price_ht >=', $params['min_price']);
        }
        if (isset($params['max_price']) && $params['max_price'] !== '') {
            $builder->where('price_ht <=', $params['max_price']);
        }

        // Filtre par statut
        if (isset($params['is_archived'])) {
            $builder->where('is_archived', $params['is_archived']);
        } else {
            $builder->where('is_archived', false);
        }

        return $builder->countAllResults();
    }

    /**
     * Calcule le prix TTC d'un produit
     * 
     * @param array|string $product Produit (tableau avec tva_rate) ou ID
     * @return float Prix TTC
     */
    public function calculatePriceTTC($product): float
    {
        if (is_string($product)) {
            $product = $this->getProductWithRelations($product);
        }

        if (!$product) {
            return 0;
        }

        $priceHT = $product['price_ht'];
        $tvaRate = $product['tva_rate'] ?? 0;

        return round($priceHT * (1 + $tvaRate / 100), 2);
    }

    /**
     * Archive/Restore un produit
     * 
     * @param string $productId ID du produit
     * @return bool
     */
	public function toggleArchive(string $productId): bool
	{
		$product = $this->find($productId);

		if (!$product) {
			throw new \Exception("Produit introuvable");
		}

		log_message('debug', "Produit $productId before update:" . $product['is_archived']);

		// Inverser le statut actuel
		$newStatus = ($product['is_archived'] === 'f' || $product['is_archived'] === false) ? true : false;

		// Mettre à jour
		$affected = $this->update($productId, [
			'is_archived' => $newStatus
		]);


		log_message('debug', "Update affected rows: $affected");

		$productUpdated = $this->find($productId);
		log_message('debug', "Produit $productId after update:" . $productUpdated['is_archived']);

		return $newStatus; // true si archivé, false si restauré
	}


    /**
     * Archive plusieurs produits en une fois
     * 
     * @param array $productIds Tableau d'IDs de produits
     * @return bool
     */
    public function bulkArchive(array $productIds): bool
    {
        return $this->whereIn('id', $productIds)
            ->set(['is_archived' => true])
            ->update();
    }
	

    /**
     * Récupère les produits d'une catégorie
     * 
     * @param string $categoryId ID de la catégorie
     * @param bool $includeArchived Inclure les archivés
     * @return array Liste des produits
     */
    public function getByCategory(string $categoryId, bool $includeArchived = false): array
    {
        $builder = $this->where('category_id', $categoryId);

        if (!$includeArchived) {
            $builder->where('is_archived', false);
        }

        return $builder->findAll();
    }

    /**
     * Récupère un produit pour une entreprise spécifique
     * 
     * @param string $id ID du produit
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
     * Vérifie si une référence est unique
     * 
     * @param string $reference Référence à vérifier
     * @param string|null $excludeId ID à exclure (pour l'édition)
     * @return bool
     */
    public function isReferenceUnique(string $reference, ?string $excludeId = null): bool
    {
        $builder = $this->where('reference', $reference);

        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }

        return $builder->countAllResults() === 0;
    }
}
