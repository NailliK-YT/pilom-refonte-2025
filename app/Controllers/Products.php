<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\TvaRateModel;
use App\Models\CategoryModel;
use App\Models\PriceTierModel;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Contrôleur principal pour la gestion des produits
 * CRUD complet avec gestion d'images, prix dégressifs, recherche et filtres
 */
class Products extends BaseController
{
    protected ProductModel $productModel;
    protected TvaRateModel $tvaRateModel;
    protected CategoryModel $categoryModel;
    protected PriceTierModel $priceTierModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->tvaRateModel = new TvaRateModel();
        $this->categoryModel = new CategoryModel();
        $this->priceTierModel = new PriceTierModel();
    }

    /**
     * Récupère le company_id de l'utilisateur connecté
     * 
     * @return string|null
     */
    private function getCompanyId(): ?string
    {
        $companyId = session()->get('company_id');
        
        if (!$companyId) {
            $userId = session()->get('user_id');
            if ($userId) {
                $db = \Config\Database::connect();
                $user = $db->table('users')->where('id', $userId)->get()->getRow();
                if ($user && isset($user->company_id)) {
                    $companyId = $user->company_id;
                    session()->set('company_id', $companyId);
                }
            }
        }
        
        return $companyId;
    }

    /**
     * Liste paginée des produits avec recherche et filtres
     */
    public function index()
    {
        $companyId = $this->getCompanyId();
        if (!$companyId) {
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter');
        }

        $perPage = 20;

		// Récupérer les filtres de session
		$sessionFilters = session()->get('product_filters') ?? [];

		// Récupération des filtres GET
		$getFilters = [
			'company_id'  => $companyId,
			'keywords'    => $this->request->getGet('search'),
			'category_id' => $this->request->getGet('category'),
			'min_price'   => $this->request->getGet('min_price'),
			'max_price'   => $this->request->getGet('max_price'),
			'status'      => $this->request->getGet('status') ?? ($sessionFilters['status'] ?? 'active'),
			'sort_by'     => $this->request->getGet('sort_by'),
			'sort_order'  => $this->request->getGet('sort_order')
		];

		// Fusionner GET + session : GET a priorité si défini
		$params = array_merge($sessionFilters, array_filter($getFilters, fn($v) => $v !== null));

		// Mettre à jour la session uniquement si ce sont de nouveaux filtres (GET)
		if (!empty(array_filter($getFilters, fn($v) => $v !== null))) {
			session()->set('product_filters', $params);
		}

        $currentPage = $this->request->getGet('page') ?? 1;
        $offset = ($currentPage - 1) * $perPage;

        $products = $this->productModel->searchProducts($params, $perPage, $offset);
        $totalProducts = $this->productModel->countSearchResults($params);

        $data = [
            'title' => 'Gestion des produits',
            'products' => $products,
            'categories' => $this->categoryModel->getForSelect($companyId),
			'statuses' => ['all' => 'Tous les statuts', 'active' => 'Actif', 'archived' => 'Archivé'],
            'filters' => $params,
            'totalProducts' => $totalProducts,
            'currentPage' => $currentPage,
            'perPage' => $perPage
        ];

        return view('products/index', $data);
    }

    /**
     * Affiche la fiche produit détaillée
     * 
     * @param string $id ID du produit
     */
    public function show(string $id)
    {
        $companyId = $this->getCompanyId();
        if (!$companyId) {
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter');
        }

        $product = $this->productModel->getProductWithRelations($id);

        if (!$product || $product['company_id'] !== $companyId) {
            return redirect()->to('/products')
                ->with('error', 'Produit introuvable');
        }

        // Récupérer les prix dégressifs
        $priceTiers = $this->priceTierModel->getPriceTiersByProduct($id);

        // Récupérer le fil d'Ariane de la catégorie
        $breadcrumb = $this->categoryModel->getBreadcrumb($product['category_id']);

        $data = [
            'title' => $product['name'],
            'product' => $product,
            'priceTiers' => $priceTiers,
            'breadcrumb' => $breadcrumb,
            'priceTTC' => $this->productModel->calculatePriceTTC($product)
        ];

        return view('products/show', $data);
    }

    /**
     * Affiche le formulaire de création
     */
    public function create()
    {
        $companyId = $this->getCompanyId();
        if (!$companyId) {
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter');
        }

        if (strtolower($this->request->getMethod()) === 'get') {
            return view('products/form', [
                'title' => 'Nouveau produit',
                'product' => null,
                'priceTiers' => [],
                'tvaRates' => $this->tvaRateModel->getAllRates($companyId),
                'categories' => $this->categoryModel->getForSelect($companyId),
                'validation' => \Config\Services::validation()
            ]);
        }

        return $this->store();
    }

    /**
     * Enregistre un nouveau produit
     */
    private function store(): RedirectResponse
    {
        $companyId = $this->getCompanyId();
        if (!$companyId) {
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter');
        }

        $rules = [
            'name' => 'required|min_length[3]|max_length[255]',
            'description' => 'permit_empty',
            'reference' => 'required|alpha_numeric_punct|max_length[100]|is_unique[products.reference]',
            'price_ht' => 'required|decimal|greater_than_equal_to[0]',
            'tva_id' => 'required',
            'category_id' => 'required',
            'product_image' => 'permit_empty|uploaded[product_image]|max_size[product_image,5120]|is_image[product_image]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        try {
            // Traiter l'upload d'image
            $imagePath = $this->handleImageUpload();


            // Préparer les données du produit
            $productData = [
                'name' => $this->request->getPost('name'),
                'description' => $this->request->getPost('description'),
                'reference' => $this->request->getPost('reference'),
                'price_ht' => $this->request->getPost('price_ht'),
                'tva_id' => $this->request->getPost('tva_id'),
                'category_id' => $this->request->getPost('category_id'),
                'company_id' => $companyId,
                'image_path' => $imagePath,
                'is_archived' => false
            ];

            // Insérer le produit
            $productId = $this->productModel->insert($productData, true);

            // Traiter les prix dégressifs
            $this->savePriceTiers($productId);

            return redirect()->to('/products')
                ->with('success', 'Produit créé avec succès');
        } catch (\Exception $e) {
            log_message('error', 'Erreur création produit: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création du produit');
        }
    }

    /**
     * Affiche le formulaire d'édition
     * 
     * @param string $id ID du produit
     */
    public function edit(string $id)
    {
        $companyId = $this->getCompanyId();
        if (!$companyId) {
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter');
        }

        $product = $this->productModel->findForCompany($id, $companyId);

        if (!$product) {
            return redirect()->to('/products')
                ->with('error', 'Produit introuvable');
        }

        if (strtolower($this->request->getMethod()) === 'get') {
            $priceTiers = $this->priceTierModel->getPriceTiersByProduct($id);

            return view('products/form', [
                'title' => 'Modifier le produit',
                'product' => $product,
                'priceTiers' => $priceTiers,
                'tvaRates' => $this->tvaRateModel->getAllRates($companyId),
                'categories' => $this->categoryModel->getForSelect($companyId),
                'validation' => \Config\Services::validation()
            ]);
        }

        return $this->update($id);
    }

    /**
     * Met à jour un produit
     * 
     * @param string $id ID du produit
     */
    private function update(string $id): RedirectResponse
    {
        $rules = [
            'name' => 'required|min_length[3]|max_length[255]',
            'description' => 'permit_empty',
            'reference' => "required|alpha_numeric_punct|max_length[100]|is_unique[products.reference,id,$id]",
            'price_ht' => 'required|decimal|greater_than_equal_to[0]',
            'tva_id' => 'required',
            'category_id' => 'required',
            'product_image' => 'permit_empty|uploaded[product_image]|max_size[product_image,5120]|is_image[product_image]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        try {
            $product = $this->productModel->find($id);

            // Traiter l'upload d'image si une nouvelle image est fournie
            $imagePath = $product['image_path'];
            $newImage = $this->handleImageUpload();
            if ($newImage) {
                // Supprimer l'ancienne image
                if ($imagePath && file_exists(WRITEPATH . 'uploads/' . $imagePath)) {
                    unlink(WRITEPATH . 'uploads/' . $imagePath);
                }
                $imagePath = $newImage;
            }

            // Préparer les données du produit
            $productData = [
                'name' => $this->request->getPost('name'),
                'description' => $this->request->getPost('description'),
                'reference' => $this->request->getPost('reference'),
                'price_ht' => $this->request->getPost('price_ht'),
                'tva_id' => $this->request->getPost('tva_id'),
                'category_id' => $this->request->getPost('category_id'),
                'image_path' => $imagePath
            ];

            // Mettre à jour le produit
            $this->productModel->update($id, $productData);

            // Supprimer les anciens prix dégressifs
            $this->priceTierModel->deleteByProduct($id);

            // Enregistrer les nouveaux prix dégressifs
            $this->savePriceTiers($id);

            return redirect()->to('/products')
                ->with('success', 'Produit modifié avec succès');
        } catch (\Exception $e) {
            log_message('error', 'Erreur modification produit: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la modification du produit');
        }
    }

    /**
     * Archive un produit (suppression logique)
     * 
     * @param string $id ID du produit
     */
    public function archive(string $id): RedirectResponse
	{
		try {
			$newStatus = $this->productModel->toggleArchive($id);

			return redirect()->to('/products')
				->with('success', $newStatus
					? 'Produit archivé avec succès'
					: 'Produit restauré avec succès'
				);

		} catch (\Exception $e) {
			log_message('error', 'Erreur archivage produit: ' . $e->getMessage());
			return redirect()->to('/products')
				->with('error', 'Une erreur est survenue lors de l\'archivage du produit');
		}
	}

    /**
     * Archive plusieurs produits en une fois
     */
    public function bulkArchive(): RedirectResponse
    {
        $productIds = $this->request->getPost('product_ids');

        if (empty($productIds) || !is_array($productIds)) {
            return redirect()->to('/products')
                ->with('error', 'Aucun produit sélectionné');
        }

        try {
            $this->productModel->bulkArchive($productIds);

            $count = count($productIds);
            return redirect()->to('/products')
                ->with('success', "$count produit(s) archivé(s) avec succès");
        } catch (\Exception $e) {
            log_message('error', 'Erreur archivage multiple: ' . $e->getMessage());
            return redirect()->to('/products')
                ->with('error', 'Une erreur est survenue lors de l\'archivage des produits');
        }
    }

    /**
     * Endpoint AJAX pour calculer le prix TTC
     */
    public function calculatePrice()
    {
        $priceHT = (float) $this->request->getPost('price_ht');
        $tvaId = $this->request->getPost('tva_id');

        if (!$tvaId) {
            return $this->response->setJSON(['error' => 'Taux de TVA manquant']);
        }

        $tvaRate = $this->tvaRateModel->find($tvaId);

        if (!$tvaRate) {
            return $this->response->setJSON(['error' => 'Taux de TVA invalide']);
        }

        $priceTTC = $this->tvaRateModel->calculatePriceTTC($priceHT, $tvaRate['rate']);

        return $this->response->setJSON([
            'price_ht' => number_format($priceHT, 2, ',', ' '),
            'price_ttc' => number_format($priceTTC, 2, ',', ' '),
            'tva_amount' => number_format($priceTTC - $priceHT, 2, ',', ' ')
        ]);
    }

    /**
     * Gère l'upload et le traitement d'une image de produit
     * 
     * @return string|null Chemin relatif de l'image ou null
     */
    private function handleImageUpload(): ?string
    {
        $file = $this->request->getFile('product_image');

        if (!$file || !$file->isValid()) {
            return null;
        }

        // Créer le répertoire de destination avec structure année/mois
        $year = date('Y');
        $month = date('m');
        //$uploadPath = WRITEPATH . "uploads/products/$year/$month";
		$uploadPath = FCPATH . "uploads/products/$year/$month";

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Générer un nom unique pour l'image
        $extension = $file->getExtension();
        $newName = $this->generateUUID() . '.' . $extension;

        // Déplacer l'image
        $file->move($uploadPath, $newName);

        // Redimensionner l'image (max 800x800)
        $this->resizeImage($uploadPath . '/' . $newName);

        // Retourner le chemin relatif
        return "products/$year/$month/$newName";
    }

    /**
     * Redimensionne une image pour qu'elle ne dépasse pas 800x800px
     * 
     * @param string $imagePath Chemin absolu de l'image
     */
    private function resizeImage(string $imagePath): void
    {
        $imageService = \Config\Services::image('gd');

        try {
            $imageService->withFile($imagePath)
                ->fit(800, 800, 'center')
                ->save($imagePath);
        } catch (\Exception $e) {
            log_message('error', 'Erreur redimensionnement image: ' . $e->getMessage());
        }
    }

    /**
     * Enregistre les prix dégressifs d'un produit
     * 
     * @param string $productId ID du produit
     */
    private function savePriceTiers(string $productId): void
    {
        $quantities = $this->request->getPost('tier_quantity');
        $prices = $this->request->getPost('tier_price');

        if (!$quantities || !$prices) {
            return;
        }

        foreach ($quantities as $index => $quantity) {
            if (!empty($quantity) && !empty($prices[$index])) {
                $this->priceTierModel->insert([
                    'product_id' => $productId,
                    'min_quantity' => (int) $quantity,
                    'price_ht' => (float) $prices[$index]
                ]);
            }
        }
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
}
