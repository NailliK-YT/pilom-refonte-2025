<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * BlogArticleModel
 * 
 * Manages blog articles with SEO optimization and related data
 */
class BlogArticleModel extends Model
{
    protected $table = 'blog_articles';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;

    protected $allowedFields = [
        'id',
        'author_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image_id',
        'featured_image_alt',
        'status',
        'published_at',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'robots',
        'og_title',
        'og_description',
        'og_image',
        'twitter_title',
        'twitter_description',
        'twitter_image',
        'allow_comments',
        'is_featured',
        'reading_time',
        'word_count',
        'view_count',
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'title' => 'required|min_length[3]|max_length[255]',
        'slug' => 'required|max_length[255]',
        'content' => 'required',
        'author_id' => 'required',
        'status' => 'permit_empty|in_list[draft,published,scheduled,archived]',
    ];

    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateId', 'calculateMetrics', 'generateSlugIfEmpty'];
    protected $beforeUpdate = ['calculateMetrics'];

    /**
     * Generate UUID for new articles
     */
    protected function generateId(array $data): array
    {
        if (!isset($data['data']['id'])) {
            $data['data']['id'] = $this->generateUUID();
        }
        return $data;
    }

    /**
     * Generate slug from title if not provided
     */
    protected function generateSlugIfEmpty(array $data): array
    {
        if (empty($data['data']['slug']) && !empty($data['data']['title'])) {
            $data['data']['slug'] = $this->createSlug($data['data']['title']);
        }
        return $data;
    }

    /**
     * Calculate reading time and word count
     */
    protected function calculateMetrics(array $data): array
    {
        if (isset($data['data']['content'])) {
            $content = strip_tags($data['data']['content']);
            $wordCount = str_word_count($content);
            $data['data']['word_count'] = $wordCount;
            // Average reading speed: 200 words per minute
            $data['data']['reading_time'] = max(1, ceil($wordCount / 200));
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
     * Create URL-friendly slug from title
     */
    public function createSlug(string $title, ?string $excludeId = null): string
    {
        // French character replacements
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
            'œ' => 'oe',
            'æ' => 'ae',
        ];

        $slug = mb_strtolower($title);
        $slug = strtr($slug, $replacements);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s_]+/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');

        // Check uniqueness
        $originalSlug = $slug;
        $counter = 1;

        while ($this->slugExists($slug, $excludeId)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Check if slug already exists
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
     * Get published articles with pagination
     */
    public function getPublishedArticles(int $limit = 10, int $offset = 0): array
    {
        return $this->select('blog_articles.*, users.first_name, users.last_name, users.email as author_email')
            ->join('users', 'users.id = blog_articles.author_id', 'left')
            ->where('blog_articles.status', 'published')
            ->where('blog_articles.published_at <=', date('Y-m-d H:i:s'))
            ->orderBy('blog_articles.published_at', 'DESC')
            ->limit($limit, $offset)
            ->findAll();
    }

    /**
     * Count published articles
     */
    public function countPublished(): int
    {
        return $this->where('status', 'published')
            ->where('published_at <=', date('Y-m-d H:i:s'))
            ->countAllResults();
    }

    /**
     * Get article by slug
     */
    public function getBySlug(string $slug): ?array
    {
        return $this->select('blog_articles.*, users.first_name, users.last_name, users.email as author_email, users.avatar as author_avatar')
            ->join('users', 'users.id = blog_articles.author_id', 'left')
            ->where('blog_articles.slug', $slug)
            ->first();
    }

    /**
     * Get articles by category
     */
    public function getByCategory(string $categoryId, int $limit = 10, int $offset = 0): array
    {
        return $this->select('blog_articles.*, users.first_name, users.last_name')
            ->join('users', 'users.id = blog_articles.author_id', 'left')
            ->join('blog_articles_categories', 'blog_articles_categories.article_id = blog_articles.id')
            ->where('blog_articles_categories.category_id', $categoryId)
            ->where('blog_articles.status', 'published')
            ->where('blog_articles.published_at <=', date('Y-m-d H:i:s'))
            ->orderBy('blog_articles.published_at', 'DESC')
            ->limit($limit, $offset)
            ->findAll();
    }

    /**
     * Get articles by tag
     */
    public function getByTag(string $tagId, int $limit = 10, int $offset = 0): array
    {
        return $this->select('blog_articles.*, users.first_name, users.last_name')
            ->join('users', 'users.id = blog_articles.author_id', 'left')
            ->join('blog_articles_tags', 'blog_articles_tags.article_id = blog_articles.id')
            ->where('blog_articles_tags.tag_id', $tagId)
            ->where('blog_articles.status', 'published')
            ->where('blog_articles.published_at <=', date('Y-m-d H:i:s'))
            ->orderBy('blog_articles.published_at', 'DESC')
            ->limit($limit, $offset)
            ->findAll();
    }

    /**
     * Get related articles based on categories
     */
    public function getRelatedArticles(string $articleId, int $limit = 3): array
    {
        // Get categories of current article
        $db = \Config\Database::connect();
        $categories = $db->table('blog_articles_categories')
            ->where('article_id', $articleId)
            ->get()
            ->getResultArray();

        if (empty($categories)) {
            // If no categories, return recent articles
            return $this->getPublishedArticles($limit);
        }

        $categoryIds = array_column($categories, 'category_id');

        return $this->select('blog_articles.*, COUNT(blog_articles_categories.category_id) as relevance')
            ->join('blog_articles_categories', 'blog_articles_categories.article_id = blog_articles.id')
            ->whereIn('blog_articles_categories.category_id', $categoryIds)
            ->where('blog_articles.id !=', $articleId)
            ->where('blog_articles.status', 'published')
            ->where('blog_articles.published_at <=', date('Y-m-d H:i:s'))
            ->groupBy('blog_articles.id')
            ->orderBy('relevance', 'DESC')
            ->orderBy('blog_articles.published_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Search articles
     */
    public function searchArticles(string $query, int $limit = 10, int $offset = 0): array
    {
        $searchTerm = '%' . $query . '%';

        return $this->select('blog_articles.*, users.first_name, users.last_name')
            ->join('users', 'users.id = blog_articles.author_id', 'left')
            ->groupStart()
            ->like('blog_articles.title', $query)
            ->orLike('blog_articles.content', $query)
            ->orLike('blog_articles.excerpt', $query)
            ->groupEnd()
            ->where('blog_articles.status', 'published')
            ->where('blog_articles.published_at <=', date('Y-m-d H:i:s'))
            ->orderBy('blog_articles.published_at', 'DESC')
            ->limit($limit, $offset)
            ->findAll();
    }

    /**
     * Increment view count
     */
    public function incrementViewCount(string $articleId): bool
    {
        return $this->set('view_count', 'view_count + 1', false)
            ->where('id', $articleId)
            ->update();
    }

    /**
     * Get featured articles
     */
    public function getFeaturedArticles(int $limit = 5): array
    {
        return $this->select('blog_articles.*, users.first_name, users.last_name')
            ->join('users', 'users.id = blog_articles.author_id', 'left')
            ->where('blog_articles.is_featured', true)
            ->where('blog_articles.status', 'published')
            ->where('blog_articles.published_at <=', date('Y-m-d H:i:s'))
            ->orderBy('blog_articles.published_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get articles for admin listing
     */
    public function getForAdmin(?string $status = null, int $limit = 20, int $offset = 0): array
    {
        $builder = $this->select('blog_articles.*, users.first_name, users.last_name')
            ->join('users', 'users.id = blog_articles.author_id', 'left');

        if ($status) {
            $builder->where('blog_articles.status', $status);
        }

        return $builder->orderBy('blog_articles.updated_at', 'DESC')
            ->limit($limit, $offset)
            ->findAll();
    }

    /**
     * Get article with all related data (categories, tags)
     */
    public function getWithRelations(string $id): ?array
    {
        $article = $this->find($id);

        if (!$article) {
            return null;
        }

        $db = \Config\Database::connect();

        // Get categories
        $article['categories'] = $db->table('blog_categories')
            ->select('blog_categories.*')
            ->join('blog_articles_categories', 'blog_articles_categories.category_id = blog_categories.id')
            ->where('blog_articles_categories.article_id', $id)
            ->get()
            ->getResultArray();

        // Get tags
        $article['tags'] = $db->table('blog_tags')
            ->select('blog_tags.*')
            ->join('blog_articles_tags', 'blog_articles_tags.article_id = blog_tags.id')
            ->where('blog_articles_tags.article_id', $id)
            ->get()
            ->getResultArray();

        return $article;
    }

    /**
     * Sync article categories
     */
    public function syncCategories(string $articleId, array $categoryIds): void
    {
        $db = \Config\Database::connect();

        // Delete existing
        $db->table('blog_articles_categories')
            ->where('article_id', $articleId)
            ->delete();

        // Insert new
        foreach ($categoryIds as $categoryId) {
            $db->table('blog_articles_categories')->insert([
                'article_id' => $articleId,
                'category_id' => $categoryId,
            ]);
        }
    }

    /**
     * Sync article tags
     */
    public function syncTags(string $articleId, array $tagIds): void
    {
        $db = \Config\Database::connect();

        // Delete existing
        $db->table('blog_articles_tags')
            ->where('article_id', $articleId)
            ->delete();

        // Insert new
        foreach ($tagIds as $tagId) {
            $db->table('blog_articles_tags')->insert([
                'article_id' => $articleId,
                'tag_id' => $tagId,
            ]);
        }
    }

    /**
     * Publish an article
     */
    public function publish(string $articleId): bool
    {
        return $this->update($articleId, [
            'status' => 'published',
            'published_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Schedule article publication
     */
    public function schedule(string $articleId, string $publishDate): bool
    {
        return $this->update($articleId, [
            'status' => 'scheduled',
            'published_at' => $publishDate,
        ]);
    }

    /**
     * Get scheduled articles that should be published
     */
    public function getScheduledToPublish(): array
    {
        return $this->where('status', 'scheduled')
            ->where('published_at <=', date('Y-m-d H:i:s'))
            ->findAll();
    }

    /**
     * Get statistics for dashboard
     */
    public function getStats(): array
    {
        return [
            'total' => $this->countAllResults(false),
            'published' => $this->where('status', 'published')->countAllResults(false),
            'draft' => $this->where('status', 'draft')->countAllResults(false),
            'scheduled' => $this->where('status', 'scheduled')->countAllResults(false),
            'total_views' => $this->selectSum('view_count')->first()['view_count'] ?? 0,
        ];
    }

    /**
     * Get articles for sitemap
     */
    public function getForSitemap(): array
    {
        return $this->select('slug, updated_at, published_at')
            ->where('status', 'published')
            ->where('published_at <=', date('Y-m-d H:i:s'))
            ->findAll();
    }
}
