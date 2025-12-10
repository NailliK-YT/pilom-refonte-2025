<?php

namespace App\Controllers\Blog;

use App\Controllers\BaseController;
use App\Models\BlogCommentModel;

/**
 * BlogCommentAdminController
 * 
 * Admin controller for moderating comments
 */
class BlogCommentAdminController extends BaseController
{
    protected BlogCommentModel $commentModel;

    public function __construct()
    {
        $this->commentModel = new BlogCommentModel();
    }

    /**
     * List all comments
     */
    public function index()
    {
        $status = $this->request->getGet('status');
        $comments = $this->commentModel->getForAdmin($status, 50);
        $stats = $this->commentModel->getStats();

        return view('admin/blog/comments/index', array_merge($this->data, [
            'comments' => $comments,
            'stats' => $stats,
            'currentStatus' => $status,
            'title' => 'Modération des Commentaires',
        ]));
    }

    /**
     * Pending comments
     */
    public function pending()
    {
        $comments = $this->commentModel->getPending();
        $stats = $this->commentModel->getStats();

        return view('admin/blog/comments/index', array_merge($this->data, [
            'comments' => $comments,
            'stats' => $stats,
            'currentStatus' => 'pending',
            'title' => 'Commentaires en Attente',
        ]));
    }

    /**
     * Approve comment
     */
    public function approve(string $id)
    {
        $session = session();
        $userId = $session->get('user_id');

        $result = $this->commentModel->approve($id, $userId);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => $result]);
        }

        if ($result) {
            return redirect()->back()->with('success', 'Commentaire approuvé.');
        }

        return redirect()->back()->with('error', 'Erreur lors de l\'approbation.');
    }

    /**
     * Mark as spam
     */
    public function spam(string $id)
    {
        $session = session();
        $userId = $session->get('user_id');

        $result = $this->commentModel->markAsSpam($id, $userId);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => $result]);
        }

        if ($result) {
            return redirect()->back()->with('success', 'Commentaire marqué comme spam.');
        }

        return redirect()->back()->with('error', 'Erreur.');
    }

    /**
     * Move to trash
     */
    public function trash(string $id)
    {
        $session = session();
        $userId = $session->get('user_id');

        $result = $this->commentModel->trash($id, $userId);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => $result]);
        }

        if ($result) {
            return redirect()->back()->with('success', 'Commentaire supprimé.');
        }

        return redirect()->back()->with('error', 'Erreur lors de la suppression.');
    }

    /**
     * Bulk approve
     */
    public function bulkApprove()
    {
        $ids = $this->request->getPost('comment_ids') ?? [];

        if (empty($ids)) {
            return redirect()->back()->with('error', 'Aucun commentaire sélectionné.');
        }

        $session = session();
        $userId = $session->get('user_id');

        $count = $this->commentModel->bulkApprove($ids, $userId);

        return redirect()->back()->with('success', $count . ' commentaire(s) approuvé(s).');
    }

    /**
     * Permanent delete
     */
    public function delete(string $id)
    {
        $result = $this->commentModel->delete($id);

        if ($result) {
            return redirect()->back()->with('success', 'Commentaire supprimé définitivement.');
        }

        return redirect()->back()->with('error', 'Erreur lors de la suppression.');
    }
}
