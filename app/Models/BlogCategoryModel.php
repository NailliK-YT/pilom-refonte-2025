<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * BlogCategoryModel
 * 
 * Manages blog categories with hierarchical support
 */
class BlogCategoryModel extends Model
{
    protected $table = 'blog_categories';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;

    protected $allowedFields = [
        'id',
        'parent_id',
        'name',
        'slug',
        'description',
        'meta_title',
        'meta_description',
        'sort_order',
        'is_active',
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'name' => 'required|min_length[2]|max_length[100]',
        'slug' => 'required|max_length[100]',
    ];

    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateId', 'generateSlugIfEmpty'];

    /**
     * Generate UUID for new categories
     */
    protected function generateId(array $data): array
    {
        if (!isset($data['data']['id'])) {
            $data['data']['id'] = $this->generateUUID();
        }
        return $data;
    }

    /**
     * Generate slug from name if not provided
     */
    protected function generateSlugIfEmpty(array $data): array
    {
        if (empty($data['data']['slug']) && !empty($data['data']['name'])) {
            $data['data']['slug'] = $this->createSlug($data['data']['name']);
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
     * Create URL-friendly slug
     */
    public function createSlug(string $name, ?string $excludeId = null): string
    {
        $replacements = [
            'é' => 'e',
            'è' => 'e',
            'ê' => 'e',
            'ë' => 'e',
            'à' => 'a',
            'â' => 'a',
            'ä' => 'a',
            'ù' => 'u',
            'û' => 'u',
            'ü' => 'u',
            'î' => 'i',
            'ï' => 'i',
            'ô' => 'o',
            'ö' => 'o',
            'ç' => 'c',
        ];

        $slug = mb_strtolower($name);
        $slug = strtr($slug, $replacements);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s_]+/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');

        $originalSlug = $slug;
        $counter = 1;

        while ($this->slugExists($slug, $excludeId)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Check if slug exists
     */
    public function slugExists(string $slug, ?string $excludeId = null): bool
    {
        $builder = $this->where('slug', $slug);
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        return $builder->countAllResults() > 0;
    }

    /**
     * Get all active categories
     */
    public function getActive(): array
    {
        return $this->where('is_active', true)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    /**
     * Get category by slug
     */
    public function getBySlug(string $slug): ?array
    {
        return $this->where('slug', $slug)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Get hierarchical category tree
     */
    public function getTree(): array
    {
        $categories = $this->where('is_active', true)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('name', 'ASC')
            ->findAll();

        return $this->buildTree($categories);
    }

    /**
     * Build tree structure from flat array
     */
    private function buildTree(array $categories, ?string $parentId = null): array
    {
        $tree = [];

        foreach ($categories as $category) {
            if ($category['parent_id'] === $parentId) {
                $children = $this->buildTree($categories, $category['id']);
                if ($children) {
                    $category['children'] = $children;
                }
                $tree[] = $category;
            }
        }

        return $tree;
    }

    /**
     * Get article count for category
     */
    public function getArticleCount(string $categoryId): int
    {
        $db = \Config\Database::connect();

        return $db->table('blog_articles_categories')
            ->join('blog_articles', 'blog_articles.id = blog_articles_categories.article_id')
            ->where('blog_articles_categories.category_id', $categoryId)
            ->where('blog_articles.status', 'published')
            ->where('blog_articles.published_at <=', date('Y-m-d H:i:s'))
            ->countAllResults();
    }

    /**
     * Get categories with article counts
     */
    public function getWithCounts(): array
    {
        $categories = $this->getActive();

        foreach ($categories as &$category) {
            $category['article_count'] = $this->getArticleCount($category['id']);
        }

        return $categories;
    }

    /**
     * Get categories for an article
     */
    public function getForArticle(string $articleId): array
    {
        $db = \Config\Database::connect();

        return $db->table('blog_categories')
            ->select('blog_categories.*')
            ->join('blog_articles_categories', 'blog_articles_categories.category_id = blog_categories.id')
            ->where('blog_articles_categories.article_id', $articleId)
            ->get()
            ->getResultArray();
    }

    /**
     * Check if category has children
     */
    public function hasChildren(string $categoryId): bool
    {
        return $this->where('parent_id', $categoryId)->countAllResults() > 0;
    }

    /**
     * Get all for admin with counts
     */
    public function getForAdmin(): array
    {
        $categories = $this->orderBy('sort_order', 'ASC')
            ->orderBy('name', 'ASC')
            ->findAll();

        foreach ($categories as &$category) {
            $category['article_count'] = $this->getArticleCount($category['id']);
        }

        return $categories;
    }
}
