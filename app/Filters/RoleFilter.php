<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\PermissionModel;

class RoleFilter implements FilterInterface
{
    /**
     * Check if user has required permission
     *
     * @param RequestInterface $request
     * @param array|null $arguments - Permission name(s) required
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $userId = $session->get('user_id');

        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter.');
        }

        if (empty($arguments)) {
            return; // No specific permission required
        }

        $permissionModel = new PermissionModel();
        
        // Check if user has ANY of the required permissions
        foreach ($arguments as $permission) {
            if ($permissionModel->userHasPermission($userId, $permission)) {
                return; // User has permission, allow access
            }
        }

        // User doesn't have any required permission
        return redirect()->to('/dashboard')->with('error', 'Accès non autorisé.');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nothing to do after
    }
}
