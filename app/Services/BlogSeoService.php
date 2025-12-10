<?php

namespace App\Services;

use Config\Seo;

/**
 * BlogSeoService
 * 
 * Generates SEO tags and Schema.org structured data for blog articles
 */
class BlogSeoService
{
    protected Seo $config;
    protected SeoService $seoService;

    public function __construct()
    {
        $this->config = config('Seo');
        $this->seoService = new SeoService();
    }

    /**
     * Generate all SEO data for an article
     */
    public function generateArticleSeo(array $article): array
    {
        $title = $article['meta_title'] ?? $article['title'];
        $description = $article['meta_description'] ?? $this->generateExcerpt($article['content'], 160);
        $url = base_url('blog/' . $article['slug']);
        $image = $this->getArticleImage($article);

        return [
            'title' => $this->seoService->generateTitle($title),
            'description' => $description,
            'canonical' => $article['canonical_url'] ?? $url,
            'robots' => $article['robots'] ?? 'index, follow',
            'og_type' => 'article',
            'og_title' => $article['og_title'] ?? $title,
            'og_description' => $article['og_description'] ?? $description,
            'og_image' => $article['og_image'] ?? $image,
            'twitter_title' => $article['twitter_title'] ?? $title,
            'twitter_description' => $article['twitter_description'] ?? $description,
            'twitter_image' => $article['twitter_image'] ?? $image,
        ];
    }

    /**
     * Generate Schema.org Article
     */
    public function schemaArticle(array $article, ?array $author = null): string
    {
        $url = base_url('blog/' . $article['slug']);
        $image = $this->getArticleImage($article);

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $article['title'],
            'description' => $article['meta_description'] ?? $this->generateExcerpt($article['content'], 160),
            'url' => $url,
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $url,
            ],
            'datePublished' => $article['published_at'] ?? $article['created_at'],
            'dateModified' => $article['updated_at'],
            'wordCount' => $article['word_count'] ?? 0,
        ];

        if ($image) {
            $schema['image'] = [
                '@type' => 'ImageObject',
                'url' => $image,
            ];
        }

        if ($author) {
            $authorName = trim(($author['first_name'] ?? '') . ' ' . ($author['last_name'] ?? ''));
            if (empty($authorName)) {
                $authorName = $author['email'] ?? 'Pilom';
            }

            $schema['author'] = [
                '@type' => 'Person',
                'name' => $authorName,
            ];
        }

        $schema['publisher'] = [
            '@type' => 'Organization',
            'name' => 'Pilom',
            'url' => base_url(),
            'logo' => [
                '@type' => 'ImageObject',
                'url' => base_url('images/logo.png'),
            ],
        ];

        return $this->wrapSchema($schema);
    }

    /**
     * Generate Schema.org BlogPosting (more detailed than Article)
     */
    public function schemaBlogPosting(array $article, ?array $author = null, array $categories = [], array $tags = []): string
    {
        $url = base_url('blog/' . $article['slug']);
        $image = $this->getArticleImage($article);

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => $article['title'],
            'description' => $article['meta_description'] ?? $this->generateExcerpt($article['content'], 160),
            'articleBody' => strip_tags($article['content']),
            'url' => $url,
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $url,
            ],
            'datePublished' => $article['published_at'] ?? $article['created_at'],
            'dateModified' => $article['updated_at'],
            'wordCount' => $article['word_count'] ?? 0,
            'timeRequired' => 'PT' . max(1, $article['reading_time'] ?? 1) . 'M',
        ];

        if ($image) {
            $schema['image'] = [
                '@type' => 'ImageObject',
                'url' => $image,
                'caption' => $article['featured_image_alt'] ?? $article['title'],
            ];
        }

        if ($author) {
            $authorName = trim(($author['first_name'] ?? '') . ' ' . ($author['last_name'] ?? ''));
            if (empty($authorName)) {
                $authorName = $author['email'] ?? 'Pilom';
            }

            $schema['author'] = [
                '@type' => 'Person',
                'name' => $authorName,
            ];
        }

        // Add keywords from tags
        if (!empty($tags)) {
            $schema['keywords'] = implode(', ', array_column($tags, 'name'));
        }

        // Add article sections from categories
        if (!empty($categories)) {
            $schema['articleSection'] = array_column($categories, 'name');
        }

        $schema['publisher'] = [
            '@type' => 'Organization',
            'name' => 'Pilom',
            'url' => base_url(),
            'logo' => [
                '@type' => 'ImageObject',
                'url' => base_url('images/logo.png'),
            ],
        ];

        return $this->wrapSchema($schema);
    }

    /**
     * Generate Schema.org BreadcrumbList for blog
     */
    public function schemaBreadcrumb(array $items): string
    {
        $listItems = [];
        $position = 1;

        foreach ($items as $item) {
            $listItems[] = [
                '@type' => 'ListItem',
                'position' => $position,
                'name' => $item['name'],
                'item' => $item['url'],
            ];
            $position++;
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $listItems,
        ];

        return $this->wrapSchema($schema);
    }

    /**
     * Generate breadcrumb data for an article
     */
    public function getArticleBreadcrumbs(array $article, ?array $category = null): array
    {
        $items = [
            ['name' => 'Accueil', 'url' => base_url()],
            ['name' => 'Blog', 'url' => base_url('blog')],
        ];

        if ($category) {
            $items[] = [
                'name' => $category['name'],
                'url' => base_url('blog/categorie/' . $category['slug']),
            ];
        }

        $items[] = [
            'name' => $article['title'],
            'url' => base_url('blog/' . $article['slug']),
        ];

        return $items;
    }

    /**
     * Generate category SEO data
     */
    public function generateCategorySeo(array $category): array
    {
        $title = $category['meta_title'] ?? $category['name'] . ' - Blog Pilom';
        $description = $category['meta_description'] ?? 'Découvrez nos articles sur ' . $category['name'] . '. Conseils et astuces pour les professionnels.';

        return [
            'title' => $this->seoService->generateTitle($title),
            'description' => $description,
            'canonical' => base_url('blog/categorie/' . $category['slug']),
            'robots' => 'index, follow',
            'og_type' => 'website',
            'og_title' => $title,
            'og_description' => $description,
        ];
    }

    /**
     * Analyze article content for SEO
     */
    public function analyzeContent(string $content): array
    {
        $plainText = strip_tags($content);
        $wordCount = str_word_count($plainText);

        // Check for headings structure
        preg_match_all('/<h([1-6])[^>]*>/i', $content, $headings);
        $hasH1 = in_array('1', $headings[1] ?? []);
        $hasH2 = in_array('2', $headings[1] ?? []);

        // Check for images with alt
        preg_match_all('/<img[^>]+alt=["\']([^"\']*)["\'][^>]*>/i', $content, $alts);
        preg_match_all('/<img[^>]*>/i', $content, $allImages);
        $imagesWithAlt = count(array_filter($alts[1] ?? []));
        $totalImages = count($allImages[0] ?? []);

        // Check for links
        preg_match_all('/<a[^>]+href/i', $content, $links);
        $linkCount = count($links[0] ?? []);

        // Readability (sentences per paragraph average)
        $sentences = preg_split('/[.!?]+/', $plainText, -1, PREG_SPLIT_NO_EMPTY);
        $avgSentenceLength = $wordCount / max(1, count($sentences));

        return [
            'word_count' => $wordCount,
            'reading_time' => max(1, ceil($wordCount / 200)),
            'has_h1' => $hasH1,
            'has_h2' => $hasH2,
            'headings_count' => count($headings[1] ?? []),
            'images_total' => $totalImages,
            'images_with_alt' => $imagesWithAlt,
            'links_count' => $linkCount,
            'avg_sentence_length' => round($avgSentenceLength, 1),
            'readability_score' => $this->calculateReadabilityScore($plainText),
        ];
    }

    /**
     * Calculate simple readability score (0-100)
     */
    private function calculateReadabilityScore(string $text): int
    {
        $sentences = preg_split('/[.!?]+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        $words = str_word_count($text);
        $syllables = $this->countSyllables($text);

        if (count($sentences) === 0 || $words === 0) {
            return 50;
        }

        // Simple Flesch-Kincaid formula adapted
        $wordsPerSentence = $words / count($sentences);
        $syllablesPerWord = $syllables / $words;

        $score = 206.835 - (1.015 * $wordsPerSentence) - (84.6 * $syllablesPerWord);

        return max(0, min(100, (int) $score));
    }

    /**
     * Approximate syllable count for French text
     */
    private function countSyllables(string $text): int
    {
        // Simplified syllable counting
        $words = preg_split('/\s+/', strtolower($text));
        $count = 0;

        foreach ($words as $word) {
            // Count vowel groups
            preg_match_all('/[aeiouyàâäéèêëïîôùûü]+/u', $word, $matches);
            $count += max(1, count($matches[0]));
        }

        return $count;
    }

    /**
     * Get article image URL
     */
    private function getArticleImage(array $article): ?string
    {
        if (!empty($article['og_image'])) {
            return $article['og_image'];
        }

        if (!empty($article['featured_image_id'])) {
            $mediaModel = new \App\Models\BlogMediaModel();
            $media = $mediaModel->find($article['featured_image_id']);
            if ($media) {
                return base_url($media['file_path']);
            }
        }

        return base_url('images/og-default.jpg');
    }

    /**
     * Generate excerpt from content
     */
    private function generateExcerpt(string $content, int $length = 160): string
    {
        $text = strip_tags($content);
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);

        if (strlen($text) <= $length) {
            return $text;
        }

        $excerpt = substr($text, 0, $length);
        $lastSpace = strrpos($excerpt, ' ');

        if ($lastSpace !== false) {
            $excerpt = substr($excerpt, 0, $lastSpace);
        }

        return $excerpt . '...';
    }

    /**
     * Wrap schema in JSON-LD script tag
     */
    private function wrapSchema(array $schema): string
    {
        return '<script type="application/ld+json">' .
            json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) .
            '</script>';
    }
}
