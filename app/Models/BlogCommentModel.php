<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * BlogCommentModel
 * 
 * Manages blog comments with threading and moderation
 */
class BlogCommentModel extends Model
{
    protected $table = 'blog_comments';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;

    protected $allowedFields = [
        'id',
        'article_id',
        'parent_id',
        'user_id',
        'author_name',
        'author_email',
        'author_website',
        'content',
        'status',
        'moderated_by',
        'moderated_at',
        'ip_address',
        'user_agent',
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'article_id' => 'required',
        'author_name' => 'required|min_length[2]|max_length[100]',
        'author_email' => 'required|valid_email|max_length[255]',
        'content' => 'required|min_length[3]',
    ];

    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateId', 'sanitizeContent'];

    protected function generateId(array $data): array
    {
        if (!isset($data['data']['id'])) {
            $data['data']['id'] = $this->generateUUID();
        }
        return $data;
    }

    /**
     * Basic content sanitization
     */
    protected function sanitizeContent(array $data): array
    {
        if (isset($data['data']['content'])) {
            // Strip HTML tags but keep line breaks
            $data['data']['content'] = strip_tags($data['data']['content']);
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
     * Get approved comments for an article (threaded)
     */
    public function getApprovedForArticle(string $articleId): array
    {
        $comments = $this->where('article_id', $articleId)
            ->where('status', 'approved')
            ->orderBy('created_at', 'ASC')
            ->findAll();

        return $this->buildThread($comments);
    }

    /**
     * Build threaded comment structure
     */
    private function buildThread(array $comments, ?string $parentId = null): array
    {
        $thread = [];

        foreach ($comments as $comment) {
            if ($comment['parent_id'] === $parentId) {
                $replies = $this->buildThread($comments, $comment['id']);
                if ($replies) {
                    $comment['replies'] = $replies;
                }
                $thread[] = $comment;
            }
        }

        return $thread;
    }

    /**
     * Count approved comments for an article
     */
    public function countForArticle(string $articleId): int
    {
        return $this->where('article_id', $articleId)
            ->where('status', 'approved')
            ->countAllResults();
    }

    /**
     * Get pending comments for moderation
     */
    public function getPending(int $limit = 50): array
    {
        return $this->select('blog_comments.*, blog_articles.title as article_title, blog_articles.slug as article_slug')
            ->join('blog_articles', 'blog_articles.id = blog_comments.article_id', 'left')
            ->where('blog_comments.status', 'pending')
            ->orderBy('blog_comments.created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Count pending comments
     */
    public function countPending(): int
    {
        return $this->where('status', 'pending')->countAllResults();
    }

    /**
     * Approve a comment
     */
    public function approve(string $commentId, string $moderatorId): bool
    {
        return $this->update($commentId, [
            'status' => 'approved',
            'moderated_by' => $moderatorId,
            'moderated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Mark as spam
     */
    public function markAsSpam(string $commentId, string $moderatorId): bool
    {
        return $this->update($commentId, [
            'status' => 'spam',
            'moderated_by' => $moderatorId,
            'moderated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Move to trash
     */
    public function trash(string $commentId, string $moderatorId): bool
    {
        return $this->update($commentId, [
            'status' => 'trash',
            'moderated_by' => $moderatorId,
            'moderated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Get all comments for admin with filters
     */
    public function getForAdmin(?string $status = null, int $limit = 50, int $offset = 0): array
    {
        $builder = $this->select('blog_comments.*, blog_articles.title as article_title, blog_articles.slug as article_slug')
            ->join('blog_articles', 'blog_articles.id = blog_comments.article_id', 'left');

        if ($status) {
            $builder->where('blog_comments.status', $status);
        }

        return $builder->orderBy('blog_comments.created_at', 'DESC')
            ->limit($limit, $offset)
            ->findAll();
    }

    /**
     * Get stats for dashboard
     */
    public function getStats(): array
    {
        return [
            'total' => $this->countAllResults(false),
            'pending' => $this->where('status', 'pending')->countAllResults(false),
            'approved' => $this->where('status', 'approved')->countAllResults(false),
            'spam' => $this->where('status', 'spam')->countAllResults(false),
        ];
    }

    /**
     * Bulk approve comments
     */
    public function bulkApprove(array $commentIds, string $moderatorId): int
    {
        return $this->whereIn('id', $commentIds)
            ->set([
                'status' => 'approved',
                'moderated_by' => $moderatorId,
                'moderated_at' => date('Y-m-d H:i:s'),
            ])
            ->update();
    }

    /**
     * Simple spam check (honeypot and time-based)
     */
    public function isLikelySpam(array $data, int $formLoadTime): bool
    {
        // Check if form was submitted too quickly (bot behavior)
        $submissionTime = time();
        if (($submissionTime - $formLoadTime) < 3) {
            return true;
        }

        // Check for common spam patterns in content
        $spamPatterns = [
            '/\[url=/i',
            '/\[link=/i',
            '/http.*http/i', // Multiple URLs
            '/viagra|cialis|casino|poker/i',
        ];

        foreach ($spamPatterns as $pattern) {
            if (preg_match($pattern, $data['content'] ?? '')) {
                return true;
            }
        }

        return false;
    }
}
