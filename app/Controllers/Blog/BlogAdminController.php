<?php

namespace App\Controllers\Blog;

use App\Controllers\BaseController;
use App\Models\BlogArticleModel;
use App\Models\BlogCategoryModel;
use App\Models\BlogTagModel;
use App\Models\BlogArticleVersionModel;
use App\Models\BlogMediaModel;
use App\Services\BlogService;
use App\Services\BlogSeoService;

/**
 * BlogAdminController
 * 
 * Admin controller for managing blog articles
 */
class BlogAdminController extends BaseController
{
    protected BlogArticleModel $articleModel;
    protected BlogCategoryModel $categoryModel;
    protected BlogTagModel $tagModel;
    protected BlogArticleVersionModel $versionModel;
    protected BlogMediaModel $mediaModel;
    protected BlogService $blogService;
    protected BlogSeoService $seoService;

    public function __construct()
    {
        $this->articleModel = new BlogArticleModel();
        $this->categoryModel = new BlogCategoryModel();
        $this->tagModel = new BlogTagModel();
        $this->versionModel = new BlogArticleVersionModel();
        $this->mediaModel = new BlogMediaModel();
        $this->blogService = new BlogService();
        $this->seoService = new BlogSeoService();
    }

    /**
     * List all articles
     */
    public function index()
    {
        $status = $this->request->getGet('status');
        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $articles = $this->articleModel->getForAdmin($status, $perPage, $offset);
        $stats = $this->articleModel->getStats();

        return view('admin/blog/articles/index', array_merge($this->data, [
            'articles' => $articles,
            'stats' => $stats,
            'currentStatus' => $status,
            'title' => 'Gestion du Blog',
        ]));
    }

    /**
     * Create article form
     */
    public function create()
    {
        $categories = $this->categoryModel->getActive();
        $tags = $this->tagModel->findAll();

        return view('admin/blog/articles/form', array_merge($this->data, [
            'article' => null,
            'categories' => $categories,
            'tags' => $tags,
            'selectedCategories' => [],
            'selectedTags' => [],
            'title' => 'Nouvel Article',
            'isEdit' => false,
        ]));
    }

    /**
     * Store new article
     */
    public function store()
    {
        $rules = [
            'title' => 'required|min_length[3]|max_length[255]',
            'content' => 'required|min_length[10]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $session = session();
        $userId = $session->get('user_id');

        $data = [
            'author_id' => $userId,
            'title' => $this->request->getPost('title'),
            'slug' => $this->request->getPost('slug') ?: null,
            'content' => $this->request->getPost('content'),
            'excerpt' => $this->request->getPost('excerpt'),
            'status' => $this->request->getPost('status') ?? 'draft',
            'meta_title' => $this->request->getPost('meta_title'),
            'meta_description' => $this->request->getPost('meta_description'),
            'meta_keywords' => $this->request->getPost('meta_keywords'),
            'og_title' => $this->request->getPost('og_title'),
            'og_description' => $this->request->getPost('og_description'),
            'allow_comments' => (bool) $this->request->getPost('allow_comments'),
            'is_featured' => (bool) $this->request->getPost('is_featured'),
            'featured_image_id' => $this->request->getPost('featured_image_id') ?: null,
            'featured_image_alt' => $this->request->getPost('featured_image_alt'),
        ];

        // Handle scheduled publication
        if ($data['status'] === 'scheduled') {
            $publishDate = $this->request->getPost('published_at');
            if ($publishDate) {
                $data['published_at'] = $publishDate;
            }
        } elseif ($data['status'] === 'published') {
            $data['published_at'] = date('Y-m-d H:i:s');
        }

        $categoryIds = $this->request->getPost('categories') ?? [];
        $tagNames = array_filter(explode(',', $this->request->getPost('tags_input') ?? ''));

        $result = $this->blogService->createArticle($data, $categoryIds, $tagNames);

        if ($result['success']) {
            return redirect()->to(base_url('admin/blog'))->with('success', 'Article créé avec succès.');
        }

        return redirect()->back()->withInput()->with('error', $result['error']);
    }

    /**
     * Edit article form
     */
    public function edit(string $id)
    {
        $article = $this->articleModel->find($id);

        if (!$article) {
            return redirect()->to(base_url('admin/blog'))->with('error', 'Article non trouvé.');
        }

        $categories = $this->categoryModel->getActive();
        $tags = $this->tagModel->findAll();
        $selectedCategories = array_column($this->categoryModel->getForArticle($id), 'id');
        $selectedTags = $this->tagModel->getForArticle($id);

        // SEO score
        $seoScore = $this->blogService->calculateSeoScore($article);

        // Content analysis
        $contentAnalysis = $this->seoService->analyzeContent($article['content']);

        return view('admin/blog/articles/form', array_merge($this->data, [
            'article' => $article,
            'categories' => $categories,
            'tags' => $tags,
            'selectedCategories' => $selectedCategories,
            'selectedTags' => $selectedTags,
            'seoScore' => $seoScore,
            'contentAnalysis' => $contentAnalysis,
            'title' => 'Modifier : ' . $article['title'],
            'isEdit' => true,
        ]));
    }

    /**
     * Update article
     */
    public function update(string $id)
    {
        $article = $this->articleModel->find($id);

        if (!$article) {
            return redirect()->to(base_url('admin/blog'))->with('error', 'Article non trouvé.');
        }

        $rules = [
            'title' => 'required|min_length[3]|max_length[255]',
            'content' => 'required|min_length[10]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $session = session();
        $userId = $session->get('user_id');

        $data = [
            'author_id' => $userId,
            'title' => $this->request->getPost('title'),
            'slug' => $this->request->getPost('slug') ?: null,
            'content' => $this->request->getPost('content'),
            'excerpt' => $this->request->getPost('excerpt'),
            'status' => $this->request->getPost('status') ?? $article['status'],
            'meta_title' => $this->request->getPost('meta_title'),
            'meta_description' => $this->request->getPost('meta_description'),
            'meta_keywords' => $this->request->getPost('meta_keywords'),
            'og_title' => $this->request->getPost('og_title'),
            'og_description' => $this->request->getPost('og_description'),
            'allow_comments' => (bool) $this->request->getPost('allow_comments'),
            'is_featured' => (bool) $this->request->getPost('is_featured'),
            'featured_image_id' => $this->request->getPost('featured_image_id') ?: null,
            'featured_image_alt' => $this->request->getPost('featured_image_alt'),
        ];

        // Handle publication date
        if ($data['status'] === 'scheduled') {
            $data['published_at'] = $this->request->getPost('published_at');
        } elseif ($data['status'] === 'published' && $article['status'] !== 'published') {
            $data['published_at'] = date('Y-m-d H:i:s');
        }

        $categoryIds = $this->request->getPost('categories') ?? [];
        $tagNames = array_filter(explode(',', $this->request->getPost('tags_input') ?? ''));
        $changeSummary = $this->request->getPost('change_summary');

        $result = $this->blogService->updateArticle($id, $data, $categoryIds, $tagNames, $changeSummary);

        if ($result['success']) {
            return redirect()->to(base_url('admin/blog/edit/' . $id))->with('success', 'Article mis à jour.');
        }

        return redirect()->back()->withInput()->with('error', $result['error']);
    }

    /**
     * Delete (archive) article
     */
    public function delete(string $id)
    {
        $result = $this->blogService->archiveArticle($id);

        if ($result['success']) {
            return redirect()->to(base_url('admin/blog'))->with('success', 'Article archivé.');
        }

        return redirect()->back()->with('error', $result['error']);
    }

    /**
     * Publish article
     */
    public function publish(string $id)
    {
        $result = $this->blogService->publishArticle($id);

        if ($result['success']) {
            return redirect()->to(base_url('admin/blog'))->with('success', 'Article publié.');
        }

        return redirect()->back()->with('error', $result['error']);
    }

    /**
     * Preview article
     */
    public function preview(string $id)
    {
        $article = $this->articleModel->find($id);

        if (!$article) {
            return redirect()->to(base_url('admin/blog'))->with('error', 'Article non trouvé.');
        }

        $categories = $this->categoryModel->getForArticle($id);
        $tags = $this->tagModel->getForArticle($id);

        return view('admin/blog/articles/preview', array_merge($this->data, [
            'article' => $article,
            'categories' => $categories,
            'tags' => $tags,
            'title' => 'Prévisualisation : ' . $article['title'],
        ]));
    }

    /**
     * Duplicate article
     */
    public function duplicate(string $id)
    {
        $session = session();
        $userId = $session->get('user_id');

        $result = $this->blogService->duplicateArticle($id, $userId);

        if ($result['success']) {
            return redirect()->to(base_url('admin/blog/edit/' . $result['article_id']))
                ->with('success', 'Article dupliqué. Vous pouvez maintenant le modifier.');
        }

        return redirect()->back()->with('error', $result['error']);
    }

    /**
     * Version history
     */
    public function versions(string $id)
    {
        $article = $this->articleModel->find($id);

        if (!$article) {
            return redirect()->to(base_url('admin/blog'))->with('error', 'Article non trouvé.');
        }

        $versions = $this->versionModel->getForArticle($id);

        return view('admin/blog/articles/versions', array_merge($this->data, [
            'article' => $article,
            'versions' => $versions,
            'title' => 'Historique : ' . $article['title'],
        ]));
    }

    /**
     * Restore version
     */
    public function restore(string $versionId)
    {
        $session = session();
        $userId = $session->get('user_id');

        $result = $this->blogService->restoreVersion($versionId, $userId);

        if ($result['success']) {
            return redirect()->back()->with('success', 'Version restaurée avec succès.');
        }

        return redirect()->back()->with('error', $result['error']);
    }

    /**
     * Blog statistics dashboard
     */
    public function stats()
    {
        $stats = $this->blogService->getGlobalStats();

        // Get top articles by views
        $topArticles = $this->articleModel
            ->where('status', 'published')
            ->orderBy('view_count', 'DESC')
            ->limit(10)
            ->findAll();

        return view('admin/blog/stats', array_merge($this->data, [
            'stats' => $stats,
            'topArticles' => $topArticles,
            'title' => 'Statistiques du Blog',
        ]));
    }

    /**
     * AJAX: Upload media
     */
    public function uploadMedia()
    {
        $file = $this->request->getFile('file');

        if (!$file || !$file->isValid()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Fichier invalide.',
            ]);
        }

        // Validate file type
        $validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file->getMimeType(), $validTypes)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Type de fichier non autorisé.',
            ]);
        }

        // Max size 5MB
        if ($file->getSize() > 5 * 1024 * 1024) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Fichier trop volumineux (max 5MB).',
            ]);
        }

        $session = session();
        $userId = $session->get('user_id');

        // Generate unique filename
        $newName = $file->getRandomName();
        $uploadPath = 'uploads/blog/' . date('Y/m');

        // Create directory if not exists
        if (!is_dir(FCPATH . $uploadPath)) {
            mkdir(FCPATH . $uploadPath, 0755, true);
        }

        // Move file
        $file->move(FCPATH . $uploadPath, $newName);

        // Get image dimensions
        $imagePath = FCPATH . $uploadPath . '/' . $newName;
        $imageInfo = getimagesize($imagePath);

        // Save to database
        $mediaData = [
            'uploaded_by' => $userId,
            'filename' => $newName,
            'original_filename' => $file->getClientName(),
            'file_path' => $uploadPath . '/' . $newName,
            'file_type' => 'image',
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'width' => $imageInfo[0] ?? null,
            'height' => $imageInfo[1] ?? null,
        ];

        $this->mediaModel->insert($mediaData);
        $mediaId = $this->mediaModel->getInsertID();

        return $this->response->setJSON([
            'success' => true,
            'media_id' => $mediaId,
            'url' => base_url($mediaData['file_path']),
            'filename' => $newName,
        ]);
    }

    /**
     * AJAX: Get SEO analysis
     */
    public function analyzeSeo()
    {
        $content = $this->request->getPost('content');
        $title = $this->request->getPost('title');
        $metaTitle = $this->request->getPost('meta_title');
        $metaDescription = $this->request->getPost('meta_description');

        $article = [
            'title' => $title,
            'content' => $content,
            'meta_title' => $metaTitle,
            'meta_description' => $metaDescription,
            'slug' => url_title($title, '-', true),
            'word_count' => str_word_count(strip_tags($content)),
        ];

        $seoScore = $this->blogService->calculateSeoScore($article);
        $contentAnalysis = $this->seoService->analyzeContent($content);

        return $this->response->setJSON([
            'seo' => $seoScore,
            'content' => $contentAnalysis,
        ]);
    }
}
