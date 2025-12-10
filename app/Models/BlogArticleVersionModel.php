<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * BlogArticleVersionModel
 * 
 * Manages article version history for rollback capability
 */
class BlogArticleVersionModel extends Model
{
    protected $table = 'blog_article_versions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;

    protected $allowedFields = [
        'id',
        'article_id',
        'author_id',
        'title',
        'content',
        'excerpt',
        'version_number',
        'change_summary',
    ];

    protected $useTimestamps = false;
    protected $createdField = 'created_at';

    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateId', 'setVersionNumber'];

    protected function generateId(array $data): array
    {
        if (!isset($data['data']['id'])) {
            $data['data']['id'] = $this->generateUUID();
        }
        return $data;
    }

    /**
     * Auto-increment version number
     */
    protected function setVersionNumber(array $data): array
    {
        if (!isset($data['data']['version_number']) && isset($data['data']['article_id'])) {
            $lastVersion = $this->where('article_id', $data['data']['article_id'])
                ->orderBy('version_number', 'DESC')
                ->first();

            $data['data']['version_number'] = ($lastVersion['version_number'] ?? 0) + 1;
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
     * Get versions for an article
     */
    public function getForArticle(string $articleId): array
    {
        return $this->select('blog_article_versions.*, users.first_name, users.last_name')
            ->join('users', 'users.id = blog_article_versions.author_id', 'left')
            ->where('blog_article_versions.article_id', $articleId)
            ->orderBy('blog_article_versions.version_number', 'DESC')
            ->findAll();
    }

    /**
     * Create a version snapshot from current article state
     */
    public function createSnapshot(array $article, string $authorId, ?string $changeSummary = null): string
    {
        $id = $this->generateUUID();

        $this->insert([
            'id' => $id,
            'article_id' => $article['id'],
            'author_id' => $authorId,
            'title' => $article['title'],
            'content' => $article['content'],
            'excerpt' => $article['excerpt'] ?? null,
            'change_summary' => $changeSummary,
        ]);

        return $id;
    }

    /**
     * Get specific version
     */
    public function getVersion(string $versionId): ?array
    {
        return $this->select('blog_article_versions.*, users.first_name, users.last_name')
            ->join('users', 'users.id = blog_article_versions.author_id', 'left')
            ->find($versionId);
    }

    /**
     * Compare two versions
     */
    public function compare(string $versionId1, string $versionId2): array
    {
        $v1 = $this->find($versionId1);
        $v2 = $this->find($versionId2);

        return [
            'version1' => $v1,
            'version2' => $v2,
            'title_changed' => $v1['title'] !== $v2['title'],
            'content_changed' => $v1['content'] !== $v2['content'],
            'excerpt_changed' => $v1['excerpt'] !== $v2['excerpt'],
        ];
    }

    /**
     * Limit versions per article (keep last N versions)
     */
    public function pruneOldVersions(string $articleId, int $keepCount = 10): int
    {
        $versions = $this->where('article_id', $articleId)
            ->orderBy('version_number', 'DESC')
            ->findAll();

        if (count($versions) <= $keepCount) {
            return 0;
        }

        $toDelete = array_slice($versions, $keepCount);
        $ids = array_column($toDelete, 'id');

        return $this->whereIn('id', $ids)->delete();
    }
}
