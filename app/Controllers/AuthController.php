<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class AuthController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        helper(['form', 'url', 'cookie']);
    }

    /**
     * Affiche la page de connexion
     */
    public function login()
    {
        // Si l'utilisateur est déjà connecté, rediriger vers le dashboard
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login_view');
    }

    /**
     * Traite la tentative de connexion
     */
    public function attemptLogin()
    {
        // Règles de validation
        $rules = [
            'email' => [
                'rules'  => 'required|valid_email',
                'errors' => [
                    'required'    => 'L\'adresse email est obligatoire.',
                    'valid_email' => 'Veuillez fournir une adresse email valide.',
                ],
            ],
            'password' => [
                'rules'  => 'required|min_length[6]',
                'errors' => [
                    'required'   => 'Le mot de passe est obligatoire.',
                    'min_length' => 'Le mot de passe doit contenir au moins 6 caractères.',
                ],
            ],
        ];

        // Validation des données
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Récupération des données
        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $remember = $this->request->getPost('remember');

        // Recherche de l'utilisateur
        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            return redirect()->back()->withInput()->with('error', 'Email ou mot de passe incorrect.');
        }

        // Vérification du mot de passe
        if (!$this->userModel->verifyPassword($password, $user['password_hash'])) {
            return redirect()->back()->withInput()->with('error', 'Email ou mot de passe incorrect.');
        }

        // Création de la session
        $sessionData = [
            'user_id'    => $user['id'],
            'user'       => $user,  // Ajout de l'utilisateur complet pour compatibilité
            'company_id' => $user['company_id'] ?? null,
            'email'      => $user['email'],
            'role'       => $user['role'] ?? 'user',
            'isLoggedIn' => true,
        ];
        session()->set($sessionData);

        // Gestion du "Se souvenir de moi"
        if ($remember) {
            $this->setRememberMeCookie($user['id']);
        }

        // Redirection vers le dashboard
        return redirect()->to('/dashboard')->with('success', 'Connexion réussie !');
    }

    /**
     * Déconnexion de l'utilisateur
     */
    public function logout()
    {
        // Suppression de la session
        session()->destroy();

        // Suppression du cookie "Se souvenir de moi"
        delete_cookie('remember_token');

        return redirect()->to('/login')->with('success', 'Vous avez été déconnecté avec succès.');
    }

    /**
     * Définit le cookie "Se souvenir de moi"
     */
    private function setRememberMeCookie(string $userId)
    {
        // Génération d'un token unique
        $token = bin2hex(random_bytes(32));

        // Stockage du token dans la base de données
        $this->userModel->update($userId, ['remember_token' => hash('sha256', $token)]);

        // Création du cookie (valable 30 jours)
        $cookie = [
            'name'   => 'remember_token',
            'value'  => $token,
            'expire' => 2592000, // 30 jours en secondes
            'secure' => true,    // HTTPS uniquement
            'httponly' => true,  // Protection XSS
            'samesite' => 'Strict', // Protection CSRF
        ];

        set_cookie($cookie);
    }

    /**
     * Page "Mot de passe oublié" (placeholder)
     */
    public function forgotPassword()
    {
        return view('auth/forgot_password_view');
    }
}
