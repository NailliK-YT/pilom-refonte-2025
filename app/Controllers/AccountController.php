<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\LoginHistoryModel;
use App\Models\AccountDeletionModel;

class AccountController extends BaseController
{
    protected $userModel;
    protected $loginHistoryModel;
    protected $deletionModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->loginHistoryModel = new LoginHistoryModel();
        $this->deletionModel = new AccountDeletionModel();
        helper('form');
    }

    /**
     * Display security overview
     */
    public function security()
    {
        $userId = session()->get('user_id');

        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Vous devez être connecté.');
        }

        // Get recent login
        $recentLogins = $this->loginHistoryModel->getHistory($userId, 1);
        $lastLogin = !empty($recentLogins) ? $recentLogins[0] : null;

        // Check for deletion request
        $deletionRequest = $this->deletionModel->getPendingRequest($userId);
        $daysRemaining = $deletionRequest ? $this->deletionModel->getDaysRemaining($userId) : null;

        $data = [
            'title' => 'Sécurité du compte',
            'lastLogin' => $lastLogin,
            'deletionRequest' => $deletionRequest,
            'daysRemaining' => $daysRemaining
        ];

        return view('account/security', $data);
    }

    /**
     * Display login history
     */
    public function loginHistory()
    {
        $userId = session()->get('user_id');

        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Vous devez être connecté.');
        }

        $history = $this->loginHistoryModel->getHistory($userId, 50);

        // Parse user agents for display
        foreach ($history as &$entry) {
            $entry['parsed'] = $this->loginHistoryModel->parseUserAgent($entry['user_agent']);
        }

        $data = [
            'title' => 'Historique des connexions',
            'history' => $history
        ];

        return view('account/login_history', $data);
    }

    /**
     * Display account deletion form
     */
    public function deletion()
    {
        $userId = session()->get('user_id');

        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Vous devez être connecté.');
        }

        // Check for existing deletion request
        $deletionRequest = $this->deletionModel->getPendingRequest($userId);
        $daysRemaining = $deletionRequest ? $this->deletionModel->getDaysRemaining($userId) : null;

        $data = [
            'title' => 'Suppression du compte',
            'deletionRequest' => $deletionRequest,
            'daysRemaining' => $daysRemaining,
            'validation' => \Config\Services::validation()
        ];

        return view('account/deletion', $data);
    }

    /**
     * Request account deletion
     */
    public function requestDeletion()
    {
        $userId = session()->get('user_id');

        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Vous devez être connecté.');
        }

        // Validation
        $rules = [
            'reason' => 'permit_empty|max_length[1000]',
            'confirm' => 'required|in_list[1]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        // Create deletion request
        $reason = $this->request->getPost('reason');

        if ($this->deletionModel->requestDeletion($userId, $reason)) {
            return redirect()->to('/account/deletion')
                ->with('success', 'Demande de suppression enregistrée. Votre compte sera supprimé dans 30 jours.');
        }

        return redirect()->back()->with('error', 'Une demande de suppression est déjà en cours.');
    }

    /**
     * Cancel account deletion request
     */
    public function cancelDeletion()
    {
        $userId = session()->get('user_id');

        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Vous devez être connecté.');
        }

        if ($this->deletionModel->cancelRequest($userId)) {
            return redirect()->to('/account/deletion')
                ->with('success', 'Demande de suppression annulée. Votre compte est maintenant actif.');
        }

        return redirect()->back()->with('error', 'Aucune demande de suppression en cours.');
    }
}
