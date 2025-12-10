<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BlogArticleModel;
use App\Models\BlogCategoryModel;
use App\Models\BlogTagModel;
use App\Models\BlogCommentModel;
use App\Models\BlogNewsletterModel;
use App\Services\BlogSeoService;

/**
 * BlogController
 * 
 * Public blog pages controller
 */
class BlogController extends BaseController
{
    protected BlogArticleModel $articleModel;
    protected BlogCategoryModel $categoryModel;
    protected BlogTagModel $tagModel;
    protected BlogCommentModel $commentModel;
    protected BlogSeoService $seoService;

    protected int $articlesPerPage = 10;

    public function __construct()
    {
        $this->articleModel = new BlogArticleModel();
        $this->categoryModel = new BlogCategoryModel();
        $this->tagModel = new BlogTagModel();
        $this->commentModel = new BlogCommentModel();
        $this->seoService = new BlogSeoService();
    }

    /**
     * Blog home page - list of articles
     */
    public function index()
    {
        $page = (int) ($this->request->getGet('page') ?? 1);
        $offset = ($page - 1) * $this->articlesPerPage;

        $articles = $this->articleModel->getPublishedArticles($this->articlesPerPage, $offset);
        $totalArticles = $this->articleModel->countPublished();
        $totalPages = ceil($totalArticles / $this->articlesPerPage);

        // Attach categories to articles
        foreach ($articles as &$article) {
            $article['categories'] = $this->categoryModel->getForArticle($article['id']);
        }

        $categories = $this->categoryModel->getWithCounts();
        $popularTags = $this->tagModel->getPopular(15);
        $featuredArticles = $this->articleModel->getFeaturedArticles(3);

        $seo = [
            'title' => 'Blog - Pilom | Conseils pour entrepreneurs et artisans',
            'description' => 'Découvrez nos articles et conseils pour améliorer la gestion de votre entreprise. Facturation, gestion, conseils métier pour professionnels.',
            'canonical' => base_url('blog'),
            'og_type' => 'website',
        ];

        return view('blog/index', array_merge($this->data, [
            'articles' => $articles,
            'categories' => $categories,
            'popularTags' => $popularTags,
            'featuredArticles' => $featuredArticles,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalArticles' => $totalArticles,
            'seo' => $seo,
        ]));
    }

    /**
     * Single article page
     */
    public function show(string $slug)
    {
        $cache = \Config\Services::cache();
        $cacheKey = 'blog_article_' . $slug;

        $article = $cache->get($cacheKey);

        if ($article === null) {
            $article = $this->articleModel->getBySlug($slug);

            if (!$article) {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Article non trouvé');
            }

            // Only show published articles
            if ($article['status'] !== 'published' || strtotime($article['published_at']) > time()) {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Article non disponible');
            }

            // Cache for 1 hour
            $cache->save($cacheKey, $article, 3600);
        }

        // Increment view count (fire and forget)
        $this->articleModel->incrementViewCount($article['id']);

        $categories = $this->categoryModel->getForArticle($article['id']);
        $tags = $this->tagModel->getForArticle($article['id']);
        $comments = $this->commentModel->getApprovedForArticle($article['id']);
        $commentCount = $this->commentModel->countForArticle($article['id']);
        $relatedArticles = $this->articleModel->getRelatedArticles($article['id'], 3);

        // SEO
        $seo = $this->seoService->generateArticleSeo($article);
        $breadcrumbs = $this->seoService->getArticleBreadcrumbs($article, $categories[0] ?? null);

        // Schema.org
        $schema = $this->seoService->schemaBlogPosting($article, [
            'first_name' => $article['first_name'] ?? '',
            'last_name' => $article['last_name'] ?? '',
            'email' => $article['author_email'] ?? '',
        ], $categories, $tags);
        $schema .= $this->seoService->schemaBreadcrumb($breadcrumbs);

        // Sidebar data
        $sidebarCategories = $this->categoryModel->getWithCounts();
        $popularTags = $this->tagModel->getPopular(15);

        return view('blog/show', array_merge($this->data, [
            'article' => $article,
            'categories' => $categories,
            'tags' => $tags,
            'comments' => $comments,
            'commentCount' => $commentCount,
            'relatedArticles' => $relatedArticles,
            'sidebarCategories' => $sidebarCategories,
            'popularTags' => $popularTags,
            'breadcrumbs' => $breadcrumbs,
            'seo' => $seo,
            'schema' => $schema,
            'formLoadTime' => time(),
        ]));
    }

    /**
     * Category archive page
     */
    public function category(string $slug)
    {
        $category = $this->categoryModel->getBySlug($slug);

        if (!$category) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Catégorie non trouvée');
        }

        $page = (int) ($this->request->getGet('page') ?? 1);
        $offset = ($page - 1) * $this->articlesPerPage;

        $articles = $this->articleModel->getByCategory($category['id'], $this->articlesPerPage, $offset);

        // Get total count for pagination
        $db = \Config\Database::connect();
        $totalArticles = $db->table('blog_articles_categories')
            ->join('blog_articles', 'blog_articles.id = blog_articles_categories.article_id')
            ->where('blog_articles_categories.category_id', $category['id'])
            ->where('blog_articles.status', 'published')
            ->countAllResults();

        $totalPages = ceil($totalArticles / $this->articlesPerPage);

        $seo = $this->seoService->generateCategorySeo($category);

        $categories = $this->categoryModel->getWithCounts();
        $popularTags = $this->tagModel->getPopular(15);

        $breadcrumbs = [
            ['name' => 'Accueil', 'url' => base_url()],
            ['name' => 'Blog', 'url' => base_url('blog')],
            ['name' => $category['name'], 'url' => base_url('blog/categorie/' . $category['slug'])],
        ];

        return view('blog/category', array_merge($this->data, [
            'category' => $category,
            'articles' => $articles,
            'categories' => $categories,
            'popularTags' => $popularTags,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalArticles' => $totalArticles,
            'breadcrumbs' => $breadcrumbs,
            'seo' => $seo,
            'schema' => $this->seoService->schemaBreadcrumb($breadcrumbs),
        ]));
    }

    /**
     * Tag archive page
     */
    public function tag(string $slug)
    {
        $tag = $this->tagModel->getBySlug($slug);

        if (!$tag) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Tag non trouvé');
        }

        $page = (int) ($this->request->getGet('page') ?? 1);
        $offset = ($page - 1) * $this->articlesPerPage;

        $articles = $this->articleModel->getByTag($tag['id'], $this->articlesPerPage, $offset);

        // Get total count
        $db = \Config\Database::connect();
        $totalArticles = $db->table('blog_articles_tags')
            ->join('blog_articles', 'blog_articles.id = blog_articles_tags.article_id')
            ->where('blog_articles_tags.tag_id', $tag['id'])
            ->where('blog_articles.status', 'published')
            ->countAllResults();

        $totalPages = ceil($totalArticles / $this->articlesPerPage);

        $seo = [
            'title' => 'Articles tagués "' . $tag['name'] . '" - Blog Pilom',
            'description' => 'Tous les articles du blog Pilom sur le thème : ' . $tag['name'],
            'canonical' => base_url('blog/tag/' . $tag['slug']),
        ];

        $categories = $this->categoryModel->getWithCounts();
        $popularTags = $this->tagModel->getPopular(15);

        $breadcrumbs = [
            ['name' => 'Accueil', 'url' => base_url()],
            ['name' => 'Blog', 'url' => base_url('blog')],
            ['name' => 'Tag: ' . $tag['name'], 'url' => base_url('blog/tag/' . $tag['slug'])],
        ];

        return view('blog/tag', array_merge($this->data, [
            'tag' => $tag,
            'articles' => $articles,
            'categories' => $categories,
            'popularTags' => $popularTags,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalArticles' => $totalArticles,
            'breadcrumbs' => $breadcrumbs,
            'seo' => $seo,
            'schema' => $this->seoService->schemaBreadcrumb($breadcrumbs),
        ]));
    }

    /**
     * Search results
     */
    public function search()
    {
        $query = trim($this->request->getGet('q') ?? '');

        if (empty($query)) {
            return redirect()->to(base_url('blog'));
        }

        $page = (int) ($this->request->getGet('page') ?? 1);
        $offset = ($page - 1) * $this->articlesPerPage;

        $articles = $this->articleModel->searchArticles($query, $this->articlesPerPage, $offset);
        $totalArticles = count($articles); // Simplified - for proper pagination, need separate count query

        $seo = [
            'title' => 'Recherche : "' . esc($query) . '" - Blog Pilom',
            'description' => 'Résultats de recherche pour "' . esc($query) . '" sur le blog Pilom.',
            'robots' => 'noindex, follow',
        ];

        $categories = $this->categoryModel->getWithCounts();
        $popularTags = $this->tagModel->getPopular(15);

        return view('blog/search', array_merge($this->data, [
            'query' => $query,
            'articles' => $articles,
            'categories' => $categories,
            'popularTags' => $popularTags,
            'totalArticles' => $totalArticles,
            'seo' => $seo,
        ]));
    }

    /**
     * Add a comment to an article
     */
    public function comment(string $slug)
    {
        $article = $this->articleModel->getBySlug($slug);

        if (!$article) {
            return redirect()->to(base_url('blog'))->with('error', 'Article non trouvé.');
        }

        if (!$article['allow_comments']) {
            return redirect()->to(base_url('blog/' . $slug))->with('error', 'Les commentaires sont désactivés pour cet article.');
        }

        $rules = [
            'author_name' => 'required|min_length[2]|max_length[100]',
            'author_email' => 'required|valid_email',
            'content' => 'required|min_length[10]|max_length[2000]',
            'honeypot' => 'max_length[0]', // Honeypot field must be empty
        ];

        if (!$this->validate($rules)) {
            return redirect()->to(base_url('blog/' . $slug) . '#comment-form')
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // reCAPTCHA v3 verification
        $recaptcha = new \App\Services\RecaptchaService();
        if ($recaptcha->isEnabled()) {
            $token = $this->request->getPost('recaptcha_token');
            $verification = $recaptcha->verify($token, 'submit_comment');

            if (!$verification['success']) {
                return redirect()->to(base_url('blog/' . $slug) . '#comment-form')
                    ->withInput()
                    ->with('error', 'Vérification anti-spam échouée. Veuillez réessayer.');
            }
        }

        $formLoadTime = (int) $this->request->getPost('form_load_time');
        $data = [
            'article_id' => $article['id'],
            'parent_id' => $this->request->getPost('parent_id') ?: null,
            'author_name' => $this->request->getPost('author_name'),
            'author_email' => $this->request->getPost('author_email'),
            'author_website' => $this->request->getPost('author_website'),
            'content' => $this->request->getPost('content'),
            'status' => 'pending',
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
        ];

        // Check for logged in user
        $session = session();
        if ($session->get('user_id')) {
            $data['user_id'] = $session->get('user_id');
        }

        // Spam check
        if ($this->commentModel->isLikelySpam($data, $formLoadTime)) {
            $data['status'] = 'spam';
        }

        $this->commentModel->insert($data);

        return redirect()->to(base_url('blog/' . $slug) . '#comments')
            ->with('success', 'Votre commentaire a été soumis et sera publié après modération.');
    }

    /**
     * Newsletter subscription
     */
    public function subscribeNewsletter()
    {
        $rules = [
            'email' => 'required|valid_email',
            'honeypot' => 'max_length[0]',
        ];

        if (!$this->validate($rules)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Veuillez fournir une adresse email valide.',
                ]);
            }
            return redirect()->back()->with('error', 'Veuillez fournir une adresse email valide.');
        }

        $newsletterModel = new BlogNewsletterModel();
        $result = $newsletterModel->subscribe(
            $this->request->getPost('email'),
            $this->request->getPost('source') ?? 'blog',
            $this->request->getIPAddress()
        );

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($result);
        }

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    /**
     * Unsubscribe from newsletter
     */
    public function unsubscribeNewsletter(string $token)
    {
        $newsletterModel = new BlogNewsletterModel();
        $result = $newsletterModel->unsubscribeByToken($token);

        if ($result) {
            return view('blog/unsubscribed', array_merge($this->data, [
                'seo' => ['title' => 'Désinscription confirmée - Pilom'],
            ]));
        }

        return redirect()->to(base_url('blog'))->with('error', 'Lien de désinscription invalide.');
    }

    /**
     * RSS Feed
     */
    public function feed()
    {
        $articles = $this->articleModel->getPublishedArticles(20);

        $this->response->setContentType('application/rss+xml');

        $rss = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $rss .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">' . "\n";
        $rss .= '<channel>' . "\n";
        $rss .= '<title>Blog Pilom</title>' . "\n";
        $rss .= '<link>' . base_url('blog') . '</link>' . "\n";
        $rss .= '<description>Conseils et astuces pour les entrepreneurs et artisans</description>' . "\n";
        $rss .= '<language>fr-FR</language>' . "\n";
        $rss .= '<atom:link href="' . base_url('blog/feed') . '" rel="self" type="application/rss+xml"/>' . "\n";

        foreach ($articles as $article) {
            $rss .= '<item>' . "\n";
            $rss .= '<title>' . htmlspecialchars($article['title']) . '</title>' . "\n";
            $rss .= '<link>' . base_url('blog/' . $article['slug']) . '</link>' . "\n";
            $rss .= '<description>' . htmlspecialchars($article['excerpt'] ?? strip_tags(substr($article['content'], 0, 200))) . '</description>' . "\n";
            $rss .= '<pubDate>' . date('r', strtotime($article['published_at'])) . '</pubDate>' . "\n";
            $rss .= '<guid>' . base_url('blog/' . $article['slug']) . '</guid>' . "\n";
            $rss .= '</item>' . "\n";
        }

        $rss .= '</channel>' . "\n";
        $rss .= '</rss>';

        return $this->response->setBody($rss);
    }
}
