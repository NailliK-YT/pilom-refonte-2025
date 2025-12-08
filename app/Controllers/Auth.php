<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        if ($this->request->is('post')) {
            $rules = [
                'email' => 'required|valid_email',
                'password' => 'required|min_length[8]'
            ];

            if (! $this->validate($rules)) {
                return view('auth/login', [
                    'validation' => $this->validator
                ]);
            }

            $model = new UserModel();
            $user = $model->where('email', $this->request->getPost('email'))->first();

            if ($user && password_verify($this->request->getPost('password'), $user['password_hash'])) {
                $session = session();
                $session->set([
                    'user_id' => $user['id'],
                    'user' => $user,  // Ajout de l'utilisateur complet pour compatibilité
                    'company_id' => $user['company_id'] ?? null,
                    'role' => $user['role'],
                    'email' => $user['email'],
                    'isLoggedIn' => true
                ]);

                return redirect()->to('/dashboard');
            } else {
                return view('auth/login', [
                    'error' => 'Email ou mot de passe incorrect.'
                ]);
            }
        }

        return view('auth/login');
    }

    public function register()
    {
        if ($this->request->is('post')) {
            $rules = [
                'email' => 'required|valid_email|is_unique[users.email]',
                'password' => 'required|min_length[8]',
                'password_confirm' => 'matches[password]'
            ];

            if (! $this->validate($rules)) {
                return view('auth/register', [
                    'validation' => $this->validator
                ]);
            }

            $model = new UserModel();
            $data = [
                'email' => $this->request->getPost('email'),
                'password_hash' => $this->request->getPost('password'),
                'role' => 'user' // Default role
            ];

            $model->save($data);

            return redirect()->to('/login')->with('success', 'Inscription réussie. Connectez-vous.');
        }

        return view('auth/register');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }
}
