<?php

namespace App\Controllers\Blog;

use App\Controllers\BaseController;
use App\Models\BlogCategoryModel;

/**
 * BlogCategoryAdminController
 * 
 * Admin controller for managing blog categories
 */
class BlogCategoryAdminController extends BaseController
{
    protected BlogCategoryModel $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new BlogCategoryModel();
    }

    /**
     * List all categories
     */
    public function index()
    {
        $categories = $this->categoryModel->getForAdmin();

        return view('admin/blog/categories/index', array_merge($this->data, [
            'categories' => $categories,
            'title' => 'Catégories du Blog',
        ]));
    }

    /**
     * Create category form & process
     */
    public function create()
    {
        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'name' => 'required|min_length[2]|max_length[100]',
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $data = [
                'name' => $this->request->getPost('name'),
                'slug' => $this->request->getPost('slug') ?: null,
                'description' => $this->request->getPost('description'),
                'parent_id' => $this->request->getPost('parent_id') ?: null,
                'meta_title' => $this->request->getPost('meta_title'),
                'meta_description' => $this->request->getPost('meta_description'),
                'sort_order' => (int) $this->request->getPost('sort_order'),
                'is_active' => (bool) $this->request->getPost('is_active'),
            ];

            $this->categoryModel->insert($data);

            return redirect()->to(base_url('admin/blog/categories'))
                ->with('success', 'Catégorie créée avec succès.');
        }

        $parentCategories = $this->categoryModel->where('parent_id', null)->findAll();

        return view('admin/blog/categories/form', array_merge($this->data, [
            'category' => null,
            'parentCategories' => $parentCategories,
            'title' => 'Nouvelle Catégorie',
            'isEdit' => false,
        ]));
    }

    /**
     * Edit category form & process
     */
    public function edit(string $id)
    {
        $category = $this->categoryModel->find($id);

        if (!$category) {
            return redirect()->to(base_url('admin/blog/categories'))
                ->with('error', 'Catégorie non trouvée.');
        }

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'name' => 'required|min_length[2]|max_length[100]',
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $data = [
                'name' => $this->request->getPost('name'),
                'slug' => $this->request->getPost('slug') ?: $this->categoryModel->createSlug($this->request->getPost('name'), $id),
                'description' => $this->request->getPost('description'),
                'parent_id' => $this->request->getPost('parent_id') ?: null,
                'meta_title' => $this->request->getPost('meta_title'),
                'meta_description' => $this->request->getPost('meta_description'),
                'sort_order' => (int) $this->request->getPost('sort_order'),
                'is_active' => (bool) $this->request->getPost('is_active'),
            ];

            // Prevent setting self as parent
            if ($data['parent_id'] === $id) {
                $data['parent_id'] = null;
            }

            $this->categoryModel->update($id, $data);

            return redirect()->to(base_url('admin/blog/categories'))
                ->with('success', 'Catégorie mise à jour.');
        }

        // Don't show current category in parent options
        $parentCategories = $this->categoryModel
            ->where('parent_id', null)
            ->where('id !=', $id)
            ->findAll();

        return view('admin/blog/categories/form', array_merge($this->data, [
            'category' => $category,
            'parentCategories' => $parentCategories,
            'title' => 'Modifier : ' . $category['name'],
            'isEdit' => true,
        ]));
    }

    /**
     * Delete category
     */
    public function delete(string $id)
    {
        $category = $this->categoryModel->find($id);

        if (!$category) {
            return redirect()->back()->with('error', 'Catégorie non trouvée.');
        }

        // Check if has children
        if ($this->categoryModel->hasChildren($id)) {
            return redirect()->back()
                ->with('error', 'Impossible de supprimer : cette catégorie a des sous-catégories.');
        }

        // Check if has articles
        $articleCount = $this->categoryModel->getArticleCount($id);
        if ($articleCount > 0) {
            return redirect()->back()
                ->with('error', 'Impossible de supprimer : cette catégorie contient ' . $articleCount . ' article(s).');
        }

        $this->categoryModel->delete($id);

        return redirect()->to(base_url('admin/blog/categories'))
            ->with('success', 'Catégorie supprimée.');
    }
}
