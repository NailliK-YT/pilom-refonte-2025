<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UrlRedirectModel;

/**
 * RedirectFilter
 * 
 * Filtre pour gérer les redirections 301 depuis la table url_redirects.
 * Appliqué avant chaque requête pour rediriger les anciennes URLs.
 */
class RedirectFilter implements FilterInterface
{
    /**
     * Vérifie si l'URL actuelle doit être redirigée
     *
     * @param RequestInterface $request
     * @param array|null $arguments
     * @return ResponseInterface|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        try {
            $currentPath = '/' . trim($request->getUri()->getPath(), '/');

            $redirectModel = new UrlRedirectModel();
            $redirect = $redirectModel->findRedirect($currentPath);

            if ($redirect) {
                // Incrémente le compteur de hits
                $redirectModel->incrementHits($redirect['id']);

                // Détermine l'URL de destination
                $newUrl = $redirect['new_url'];

                // Si l'URL est relative, la convertir en absolue
                if (!preg_match('/^https?:\/\//', $newUrl)) {
                    $newUrl = base_url($newUrl);
                }

                // Effectue la redirection avec le code approprié
                $code = (int) ($redirect['redirect_code'] ?? 301);

                return redirect()->to($newUrl)->setStatusCode($code);
            }
        } catch (\Exception $e) {
            // Log l'erreur mais ne bloque pas la requête
            log_message('error', 'RedirectFilter error: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Après le traitement de la requête (non utilisé)
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param array|null $arguments
     * @return void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Rien à faire après
    }
}
