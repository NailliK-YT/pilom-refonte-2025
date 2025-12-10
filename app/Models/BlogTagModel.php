<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * BlogTagModel
 * 
 * Manages blog tags
 */
class BlogTagModel extends Model
{
    protected $table = 'blog_tags';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;

    protected $allowedFields = [
        'id',
        'name',
        'slug',
    ];

    protected $useTimestamps = false;
    protected $createdField = 'created_at';

    protected $validationRules = [
        'name' => 'required|min_length[2]|max_length[50]',
        'slug' => 'required|max_length[50]',
    ];

    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateId', 'generateSlugIfEmpty'];

    /**
     * Generate UUID
     */
    protected function generateId(array $data): array
    {
        if (!isset($data['data']['id'])) {
            $data['data']['id'] = $this->generateUUID();
        }
        return $data;
    }

    /**
     * Generate slug from name
     */
    protected function generateSlugIfEmpty(array $data): array
    {
        if (empty($data['data']['slug']) && !empty($data['data']['name'])) {
            $data['data']['slug'] = $this->createSlug($data['data']['name']);
        }
        return $data;
    }

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

    public function slugExists(string $slug, ?string $excludeId = null): bool
    {
        $builder = $this->where('slug', $slug);
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        return $builder->countAllResults() > 0;
    }

    /**
     * Get tag by slug
     */
    public function getBySlug(string $slug): ?array
    {
        return $this->where('slug', $slug)->first();
    }

    /**
     * Get all tags with article counts
     */
    public function getWithCounts(): array
    {
        $tags = $this->orderBy('name', 'ASC')->findAll();

        $db = \Config\Database::connect();

        foreach ($tags as &$tag) {
            $tag['article_count'] = $db->table('blog_articles_tags')
                ->join('blog_articles', 'blog_articles.id = blog_articles_tags.article_id')
                ->where('blog_articles_tags.tag_id', $tag['id'])
                ->where('blog_articles.status', 'published')
                ->countAllResults();
        }

        return $tags;
    }

    /**
     * Get popular tags (most used)
     */
    public function getPopular(int $limit = 10): array
    {
        $db = \Config\Database::connect();

        return $db->table('blog_tags')
            ->select('blog_tags.*, COUNT(blog_articles_tags.article_id) as article_count')
            ->join('blog_articles_tags', 'blog_articles_tags.tag_id = blog_tags.id', 'left')
            ->join('blog_articles', 'blog_articles.id = blog_articles_tags.article_id AND blog_articles.status = \'published\'', 'left')
            ->groupBy('blog_tags.id')
            ->orderBy('article_count', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    /**
     * Get tags for an article
     */
    public function getForArticle(string $articleId): array
    {
        $db = \Config\Database::connect();

        return $db->table('blog_tags')
            ->select('blog_tags.*')
            ->join('blog_articles_tags', 'blog_articles_tags.tag_id = blog_tags.id')
            ->where('blog_articles_tags.article_id', $articleId)
            ->get()
            ->getResultArray();
    }

    /**
     * Find or create tag by name
     */
    public function findOrCreate(string $name): array
    {
        $slug = $this->createSlug($name);
        $existing = $this->where('slug', $slug)->first();

        if ($existing) {
            return $existing;
        }

        $id = $this->generateUUID();
        $this->insert([
            'id' => $id,
            'name' => $name,
            'slug' => $slug,
        ]);

        return $this->find($id);
    }

    /**
     * Search tags by name
     */
    public function search(string $query, int $limit = 10): array
    {
        return $this->like('name', $query)
            ->orderBy('name', 'ASC')
            ->limit($limit)
            ->findAll();
    }
}
