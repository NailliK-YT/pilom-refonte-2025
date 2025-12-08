<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\UserProfileModel;
use App\Helpers\FileUploadHelper;

class ProfileController extends BaseController
{
    protected $userModel;
    protected $profileModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->profileModel = new UserProfileModel();
        helper('form');
    }

    /**
     * Display profile edit form
     */
    public function index()
    {
        $userId = session()->get('user_id');

        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Vous devez être connecté.');
        }

        // Get user and profile data
        $user = $this->userModel->find($userId);
        $profile = $this->profileModel->getByUserId($userId);

        $data = [
            'title' => 'Mon profil',
            'user' => $user,
            'profile' => $profile,
            'validation' => \Config\Services::validation()
        ];

        return view('profile/index', $data);
    }

    /**
     * Process profile update
     */
    public function update()
    {
        $userId = session()->get('user_id');

        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Vous devez être connecté.');
        }

        // Validation rules
        $rules = [
            'first_name' => 'permit_empty|max_length[255]',
            'last_name' => 'permit_empty|max_length[255]',
            'phone' => 'permit_empty|max_length[20]',
            'locale' => 'required|in_list[fr_FR,en_US,es_ES]',
            'timezone' => 'required|max_length[50]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Prepare data
        $data = [
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'phone' => $this->request->getPost('phone'),
            'locale' => $this->request->getPost('locale'),
            'timezone' => $this->request->getPost('timezone')
        ];

        // Upsert profile
        if ($this->profileModel->upsertProfile($userId, $data)) {
            return redirect()->to('/profile')->with('success', 'Profil mis à jour avec succès.');
        }

        return redirect()->back()->withInput()->with('error', 'Erreur lors de la mise à jour du profil.');
    }

    /**
     * Display password change form
     */
    public function password()
    {
        $data = [
            'title' => 'Changer le mot de passe',
            'validation' => \Config\Services::validation()
        ];

        return view('profile/password', $data);
    }

    /**
     * Process password change
     */
    public function changePassword()
    {
        $userId = session()->get('user_id');

        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Vous devez être connecté.');
        }

        // Validation rules
        $rules = [
            'current_password' => 'required',
            'new_password' => 'required|min_length[8]|max_length[255]',
            'confirm_password' => 'required|matches[new_password]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        // Get user
        $user = $this->userModel->find($userId);

        // Verify current password
        if (!$this->userModel->verifyPassword($this->request->getPost('current_password'), $user['password_hash'])) {
            return redirect()->back()->with('error', 'Le mot de passe actuel est incorrect.');
        }

        // Update password
        if ($this->userModel->update($userId, ['password_hash' => $this->request->getPost('new_password')])) {
            return redirect()->to('/profile')->with('success', 'Mot de passe changé avec succès.');
        }

        return redirect()->back()->with('error', 'Erreur lors du changement de mot de passe.');
    }

    /**
     * Upload profile photo (AJAX)
     */
    public function uploadPhoto()
    {
        $userId = session()->get('user_id');

        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'error' => 'Non authentifié.']);
        }

        $file = $this->request->getFile('photo');

        if (!$file) {
            return $this->response->setJSON(['success' => false, 'error' => 'Aucun fichier fourni.']);
        }

        // Upload and process photo
        $result = FileUploadHelper::uploadProfilePhoto($file, $userId);

        if (!$result['success']) {
            return $this->response->setJSON($result);
        }

        // Get old photo to delete
        $profile = $this->profileModel->getByUserId($userId);
        $oldPhoto = $profile['profile_photo'] ?? null;

        // Update profile with new photo path
        if ($this->profileModel->upsertProfile($userId, ['profile_photo' => $result['path']])) {
            // Delete old photo if exists
            if ($oldPhoto) {
                FileUploadHelper::deleteFile($oldPhoto);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Photo de profil mise à jour.',
                'url' => base_url($result['path'])
            ]);
        }

        return $this->response->setJSON(['success' => false, 'error' => 'Erreur lors de la mise à jour.']);
    }

    /**
     * Delete profile photo
     */
    public function deletePhoto()
    {
        $userId = session()->get('user_id');

        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'error' => 'Non authentifié.']);
        }

        $profile = $this->profileModel->getByUserId($userId);

        if (!$profile || !$profile['profile_photo']) {
            return $this->response->setJSON(['success' => false, 'error' => 'Aucune photo à supprimer.']);
        }

        // Delete file
        FileUploadHelper::deleteFile($profile['profile_photo']);

        // Update database
        if ($this->profileModel->upsertProfile($userId, ['profile_photo' => null])) {
            return $this->response->setJSON(['success' => true, 'message' => 'Photo supprimée.']);
        }

        return $this->response->setJSON(['success' => false, 'error' => 'Erreur lors de la suppression.']);
    }
}
