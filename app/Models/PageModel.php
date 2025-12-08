<?php

namespace App\Models;

use CodeIgniter\Model;

class PageModel extends Model
{
    protected $table = 'pages';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false; // UUID généré par PostgreSQL
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;

    protected $allowedFields = [
        'slug',
        'parent_id',
        'title',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'content',
        'is_in_menu',
        'menu_order',
        'menu_label',
        'is_in_footer',
        'footer_order',
        'is_active'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'slug' => 'required|max_length[255]|is_unique[pages.slug,id,{id}]',
        'title' => 'required|max_length[255]',
    ];

    protected $validationMessages = [
        'slug' => [
            'required' => 'Le slug est obligatoire',
            'is_unique' => 'Ce slug existe déjà',
        ],
    ];

    /**
     * Récupère une page par son slug (pour les URLs parlantes)
     * 
     * @param string $slug
     * @return array|null
     */
    public function getPageBySlug(string $slug): ?array
    {
        return $this->where('slug', $slug)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Récupère toutes les pages actives pour le menu (hiérarchie complète)
     * 
     * @return array
     */
    public function getMenuPages(): array
    {
        $pages = $this->where('is_in_menu', true)
            ->where('is_active', true)
            ->orderBy('menu_order', 'ASC')
            ->orderBy('title', 'ASC')
            ->findAll();

        return $this->buildTree($pages);
    }

    /**
     * Récupère les pages pour le footer
     * 
     * @return array
     */
    public function getFooterPages(): array
    {
        return $this->where('is_in_footer', true)
            ->where('is_active', true)
            ->orderBy('footer_order', 'ASC')
            ->orderBy('title', 'ASC')
            ->findAll();
    }

    /**
     * Construit le fil d'Ariane pour une page donnée
     * 
     * @param string $pageId
     * @return array
     */
    public function getBreadcrumb(string $pageId): array
    {
        $breadcrumb = [];
        $currentPage = $this->find($pageId);

        while ($currentPage) {
            // Ajouter la page courante au début du tableau
            array_unshift($breadcrumb, [
                'title' => $currentPage['title'],
                'slug' => $currentPage['slug'],
            ]);

            // Remonter au parent
            if ($currentPage['parent_id']) {
                $currentPage = $this->find($currentPage['parent_id']);
            } else {
                $currentPage = null;
            }
        }

        return $breadcrumb;
    }

    /**
     * Construit une arborescence hiérarchique à partir d'un tableau plat
     * 
     * @param array $pages
     * @param string|null $parentId
     * @return array
     */
    private function buildTree(array $pages, ?string $parentId = null): array
    {
        $tree = [];

        foreach ($pages as $page) {
            if ($page['parent_id'] === $parentId) {
                $children = $this->buildTree($pages, $page['id']);

                if ($children) {
                    $page['children'] = $children;
                }

                $tree[] = $page;
            }
        }

        return $tree;
    }

    /**
     * Récupère toutes les pages (pour l'admin)
     * 
     * @return array
     */
    public function getAllPagesWithParent(): array
    {
        return $this->select('pages.*, parent.title as parent_title')
            ->join('pages as parent', 'parent.id = pages.parent_id', 'left')
            ->orderBy('pages.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Vérifie si un slug est disponible
     * 
     * @param string $slug
     * @param string|null $excludeId
     * @return bool
     */
    public function isSlugAvailable(string $slug, ?string $excludeId = null): bool
    {
        $builder = $this->where('slug', $slug);

        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }

        return $builder->countAllResults() === 0;
    }
}
