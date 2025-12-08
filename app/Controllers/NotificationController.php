<?php

namespace App\Controllers;

use App\Models\NotificationPreferencesModel;
use App\Models\NotificationModel;

class NotificationController extends BaseController
{
    protected $preferencesModel;
    protected $notificationModel;

    public function __construct()
    {
        $this->preferencesModel = new NotificationPreferencesModel();
        $this->notificationModel = new NotificationModel();
        helper('form');
    }

    /**
     * Get user ID from session
     */
    private function getUserId(): ?string
    {
        return session()->get('user_id');
    }

    /**
     * Notification center - list all notifications
     */
    public function center()
    {
        $userId = $this->getUserId();
        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Vous devez être connecté.');
        }

        $notifications = $this->notificationModel->getForUser($userId, 50);
        $unreadCount = $this->notificationModel->getUnreadCount($userId);

        $data = [
            'title' => 'Centre de notifications',
            'notifications' => $notifications,
            'unreadCount' => $unreadCount
        ];

        return view('notifications/center', $data);
    }

    /**
     * Display notification preferences form
     */
    public function index()
    {
        $userId = $this->getUserId();
        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Vous devez être connecté.');
        }

        // Get or create default preferences
        $preferences = $this->preferencesModel->getByUserId($userId);

        if (!$preferences) {
            $this->preferencesModel->createDefaultPreferences($userId);
            $preferences = $this->preferencesModel->getByUserId($userId);
        }

        $data = [
            'title' => 'Préférences de notification',
            'preferences' => $preferences,
            'validation' => \Config\Services::validation()
        ];

        return view('notifications/preferences', $data);
    }

    /**
     * Update notification preferences
     */
    public function update()
    {
        $userId = $this->getUserId();
        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Vous devez être connecté.');
        }

        $preferences = [
            'email_notifications' => $this->request->getPost('email_notifications') ? true : false,
            'email_invoices' => $this->request->getPost('email_invoices') ? true : false,
            'email_quotes' => $this->request->getPost('email_quotes') ? true : false,
            'email_payments' => $this->request->getPost('email_payments') ? true : false,
            'email_marketing' => $this->request->getPost('email_marketing') ? true : false,
            'push_notifications' => $this->request->getPost('push_notifications') ? true : false,
            'inapp_notifications' => $this->request->getPost('inapp_notifications') ? true : false
        ];

        if ($this->preferencesModel->updatePreferences($userId, $preferences)) {
            return redirect()->to('/notifications/preferences')
                ->with('success', 'Préférences de notification mises à jour.');
        }

        return redirect()->back()->with('error', 'Erreur lors de la mise à jour des préférences.');
    }

    /**
     * Mark notification as read
     */
    public function markRead($id)
    {
        $userId = $this->getUserId();
        if (!$userId) {
            return $this->response->setJSON(['error' => 'Non autorisé'])->setStatusCode(401);
        }

        // Verify the notification belongs to the user
        $notification = $this->notificationModel->find($id);
        if (!$notification || $notification['user_id'] !== $userId) {
            return $this->response->setJSON(['error' => 'Notification introuvable'])->setStatusCode(404);
        }

        $this->notificationModel->markAsRead($id);
        
        return $this->response->setJSON(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllRead()
    {
        $userId = $this->getUserId();
        if (!$userId) {
            return $this->response->setJSON(['error' => 'Non autorisé'])->setStatusCode(401);
        }

        $this->notificationModel->markAllAsRead($userId);
        
        return $this->response->setJSON(['success' => true]);
    }

    /**
     * Get unread count (API endpoint for header badge)
     */
    public function unreadCount()
    {
        $userId = $this->getUserId();
        if (!$userId) {
            return $this->response->setJSON(['count' => 0]);
        }

        $count = $this->notificationModel->getUnreadCount($userId);
        
        return $this->response->setJSON(['count' => $count]);
    }

    /**
     * Delete a notification
     */
    public function delete($id)
    {
        $userId = $this->getUserId();
        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Vous devez être connecté.');
        }

        $notification = $this->notificationModel->find($id);
        if (!$notification || $notification['user_id'] !== $userId) {
            return redirect()->to('/notifications/center')->with('error', 'Notification introuvable.');
        }

        $this->notificationModel->delete($id);
        
        return redirect()->to('/notifications/center')->with('success', 'Notification supprimée.');
    }

    /**
     * Get recent notifications (for dropdown in header)
     */
    public function recent()
    {
        $userId = $this->getUserId();
        if (!$userId) {
            return $this->response->setJSON(['notifications' => []]);
        }

        $notifications = $this->notificationModel->getForUser($userId, 5, false);
        $unreadCount = $this->notificationModel->getUnreadCount($userId);
        
        return $this->response->setJSON([
            'notifications' => $notifications,
            'unreadCount' => $unreadCount
        ]);
    }
}
