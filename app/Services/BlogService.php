<?php

namespace App\Services;

use App\Models\BlogArticleModel;
use App\Models\BlogCategoryModel;
use App\Models\BlogTagModel;
use App\Models\BlogArticleVersionModel;
use App\Models\BlogMediaModel;

/**
 * BlogService
 * 
 * Orchestrates blog business logic
 */
class BlogService
{
    protected BlogArticleModel $articleModel;
    protected BlogCategoryModel $categoryModel;
    protected BlogTagModel $tagModel;
    protected BlogArticleVersionModel $versionModel;
    protected BlogMediaModel $mediaModel;

    public function __construct()
    {
        $this->articleModel = new BlogArticleModel();
        $this->categoryModel = new BlogCategoryModel();
        $this->tagModel = new BlogTagModel();
        $this->versionModel = new BlogArticleVersionModel();
        $this->mediaModel = new BlogMediaModel();
    }

    /**
     * Create a new article with categories and tags
     */
    public function createArticle(array $data, array $categoryIds = [], array $tagNames = []): array
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Generate slug if not provided
            if (empty($data['slug'])) {
                $data['slug'] = $this->articleModel->createSlug($data['title']);
            }

            // Insert article
            $articleId = $data['id'] ?? $this->generateUUID();
            $data['id'] = $articleId;

            $this->articleModel->insert($data);

            // Sync categories
            if (!empty($categoryIds)) {
                $this->articleModel->syncCategories($articleId, $categoryIds);
            }

            // Process tags
            if (!empty($tagNames)) {
                $tagIds = [];
                foreach ($tagNames as $tagName) {
                    $tag = $this->tagModel->findOrCreate(trim($tagName));
                    $tagIds[] = $tag['id'];
                }
                $this->articleModel->syncTags($articleId, $tagIds);
            }

            // Create initial version
            $article = $this->articleModel->find($articleId);
            $this->versionModel->createSnapshot($article, $data['author_id'], 'Création de l\'article');

            $db->transComplete();

            if ($db->transStatus() === false) {
                return ['success' => false, 'error' => 'Erreur lors de la création de l\'article.'];
            }

            return ['success' => true, 'article_id' => $articleId];

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'BlogService::createArticle - ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Update an article with categories and tags
     */
    public function updateArticle(string $articleId, array $data, array $categoryIds = [], array $tagNames = [], ?string $changeSummary = null): array
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Get current article for version
            $currentArticle = $this->articleModel->find($articleId);

            if (!$currentArticle) {
                return ['success' => false, 'error' => 'Article non trouvé.'];
            }

            // Create version snapshot before update
            $authorId = $data['author_id'] ?? $currentArticle['author_id'];
            $this->versionModel->createSnapshot($currentArticle, $authorId, $changeSummary ?? 'Mise à jour');

            // Regenerate slug if title changed and slug not provided
            if (isset($data['title']) && empty($data['slug'])) {
                if ($data['title'] !== $currentArticle['title']) {
                    $data['slug'] = $this->articleModel->createSlug($data['title'], $articleId);
                }
            }

            // Update article
            $this->articleModel->update($articleId, $data);

            // Sync categories
            if (!empty($categoryIds)) {
                $this->articleModel->syncCategories($articleId, $categoryIds);
            }

            // Process tags
            if (!empty($tagNames)) {
                $tagIds = [];
                foreach ($tagNames as $tagName) {
                    $tag = $this->tagModel->findOrCreate(trim($tagName));
                    $tagIds[] = $tag['id'];
                }
                $this->articleModel->syncTags($articleId, $tagIds);
            }

            // Prune old versions (keep last 10)
            $this->versionModel->pruneOldVersions($articleId);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return ['success' => false, 'error' => 'Erreur lors de la mise à jour.'];
            }

            return ['success' => true];

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'BlogService::updateArticle - ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Publish an article
     */
    public function publishArticle(string $articleId): array
    {
        $article = $this->articleModel->find($articleId);

        if (!$article) {
            return ['success' => false, 'error' => 'Article non trouvé.'];
        }

        $result = $this->articleModel->publish($articleId);

        // Clear cache
        $this->clearArticleCache($articleId);

        return ['success' => $result];
    }

    /**
     * Schedule article publication
     */
    public function scheduleArticle(string $articleId, string $publishDate): array
    {
        $article = $this->articleModel->find($articleId);

        if (!$article) {
            return ['success' => false, 'error' => 'Article non trouvé.'];
        }

        $result = $this->articleModel->schedule($articleId, $publishDate);

        return ['success' => $result];
    }

    /**
     * Restore article from a version
     */
    public function restoreVersion(string $versionId, string $userId): array
    {
        $version = $this->versionModel->find($versionId);

        if (!$version) {
            return ['success' => false, 'error' => 'Version non trouvée.'];
        }

        // Get current article
        $currentArticle = $this->articleModel->find($version['article_id']);

        if (!$currentArticle) {
            return ['success' => false, 'error' => 'Article non trouvé.'];
        }

        // Create snapshot of current state before restoring
        $this->versionModel->createSnapshot(
            $currentArticle,
            $userId,
            'Avant restauration de la version ' . $version['version_number']
        );

        // Restore content from version
        $this->articleModel->update($version['article_id'], [
            'title' => $version['title'],
            'content' => $version['content'],
            'excerpt' => $version['excerpt'],
        ]);

        $this->clearArticleCache($version['article_id']);

        return ['success' => true];
    }

    /**
     * Duplicate an article
     */
    public function duplicateArticle(string $articleId, string $authorId): array
    {
        $originalArticle = $this->articleModel->getWithRelations($articleId);

        if (!$originalArticle) {
            return ['success' => false, 'error' => 'Article non trouvé.'];
        }

        // Prepare new article data
        $newData = [
            'author_id' => $authorId,
            'title' => $originalArticle['title'] . ' (Copie)',
            'content' => $originalArticle['content'],
            'excerpt' => $originalArticle['excerpt'],
            'featured_image_id' => $originalArticle['featured_image_id'],
            'featured_image_alt' => $originalArticle['featured_image_alt'],
            'status' => 'draft',
            'meta_title' => $originalArticle['meta_title'],
            'meta_description' => $originalArticle['meta_description'],
            'allow_comments' => $originalArticle['allow_comments'],
        ];

        $categoryIds = array_column($originalArticle['categories'] ?? [], 'id');
        $tagNames = array_column($originalArticle['tags'] ?? [], 'name');

        return $this->createArticle($newData, $categoryIds, $tagNames);
    }

    /**
     * Delete article (archive)
     */
    public function archiveArticle(string $articleId): array
    {
        $article = $this->articleModel->find($articleId);

        if (!$article) {
            return ['success' => false, 'error' => 'Article non trouvé.'];
        }

        $result = $this->articleModel->update($articleId, ['status' => 'archived']);
        $this->clearArticleCache($articleId);

        return ['success' => $result];
    }

    /**
     * Permanently delete article
     */
    public function deleteArticle(string $articleId): array
    {
        $article = $this->articleModel->find($articleId);

        if (!$article) {
            return ['success' => false, 'error' => 'Article non trouvé.'];
        }

        // Note: Cascade delete will handle pivot tables and versions
        $result = $this->articleModel->delete($articleId);
        $this->clearArticleCache($articleId);

        return ['success' => $result];
    }

    /**
     * Process scheduled articles
     */
    public function publishScheduledArticles(): int
    {
        $scheduledArticles = $this->articleModel->getScheduledToPublish();
        $count = 0;

        foreach ($scheduledArticles as $article) {
            $this->articleModel->update($article['id'], ['status' => 'published']);
            $this->clearArticleCache($article['id']);
            $count++;
        }

        return $count;
    }

    /**
     * Get global blog statistics
     */
    public function getGlobalStats(): array
    {
        $articleStats = $this->articleModel->getStats();

        return [
            'articles' => $articleStats,
            'categories' => $this->categoryModel->countAllResults(),
            'tags' => $this->tagModel->countAllResults(),
        ];
    }

    /**
     * Calculate SEO score for an article
     */
    public function calculateSeoScore(array $article): array
    {
        $score = 0;
        $issues = [];
        $suggestions = [];

        // Title checks (25 points)
        if (!empty($article['meta_title'])) {
            $titleLen = strlen($article['meta_title']);
            if ($titleLen >= 50 && $titleLen <= 60) {
                $score += 25;
            } elseif ($titleLen >= 30 && $titleLen <= 70) {
                $score += 15;
                $suggestions[] = 'Le titre SEO devrait faire entre 50 et 60 caractères.';
            } else {
                $score += 5;
                $issues[] = 'La longueur du titre SEO n\'est pas optimale.';
            }
        } else {
            $issues[] = 'Le titre SEO n\'est pas défini.';
        }

        // Description checks (25 points)
        if (!empty($article['meta_description'])) {
            $descLen = strlen($article['meta_description']);
            if ($descLen >= 150 && $descLen <= 160) {
                $score += 25;
            } elseif ($descLen >= 120 && $descLen <= 180) {
                $score += 15;
                $suggestions[] = 'La meta description devrait faire entre 150 et 160 caractères.';
            } else {
                $score += 5;
                $issues[] = 'La longueur de la meta description n\'est pas optimale.';
            }
        } else {
            $issues[] = 'La meta description n\'est pas définie.';
        }

        // Featured image (15 points)
        if (!empty($article['featured_image_id'])) {
            $score += 10;
            if (!empty($article['featured_image_alt'])) {
                $score += 5;
            } else {
                $suggestions[] = 'Ajoutez un texte alternatif à l\'image mise en avant.';
            }
        } else {
            $suggestions[] = 'Ajoutez une image mise en avant.';
        }

        // Content length (20 points)
        $wordCount = $article['word_count'] ?? 0;
        if ($wordCount >= 1500) {
            $score += 20;
        } elseif ($wordCount >= 800) {
            $score += 15;
            $suggestions[] = 'Un article de plus de 1500 mots performe mieux pour le SEO.';
        } elseif ($wordCount >= 300) {
            $score += 10;
            $issues[] = 'L\'article est trop court pour un bon référencement.';
        } else {
            $issues[] = 'L\'article est beaucoup trop court.';
        }

        // Slug quality (15 points)
        if (!empty($article['slug'])) {
            $slugLen = strlen($article['slug']);
            if ($slugLen <= 60 && $slugLen >= 10) {
                $score += 15;
            } elseif ($slugLen <= 80) {
                $score += 10;
                $suggestions[] = 'Essayez de raccourcir l\'URL.';
            } else {
                $score += 5;
                $issues[] = 'L\'URL est trop longue.';
            }
        }

        return [
            'score' => min(100, $score),
            'grade' => $this->getGrade($score),
            'issues' => $issues,
            'suggestions' => $suggestions,
        ];
    }

    private function getGrade(int $score): string
    {
        if ($score >= 90)
            return 'A';
        if ($score >= 80)
            return 'B';
        if ($score >= 65)
            return 'C';
        if ($score >= 50)
            return 'D';
        return 'F';
    }

    /**
     * Clear article cache
     */
    private function clearArticleCache(string $articleId): void
    {
        $cache = \Config\Services::cache();
        $article = $this->articleModel->find($articleId);

        if ($article) {
            $cache->delete('blog_article_' . $article['slug']);
        }

        $cache->delete('blog_recent_articles');
        $cache->delete('blog_featured_articles');
        $cache->delete('site_sitemap');
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
}
