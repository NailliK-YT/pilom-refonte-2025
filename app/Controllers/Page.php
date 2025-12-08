<?php

namespace App\Controllers;

use App\Models\PageModel;

class Page extends BaseController
{
    protected $pageModel;

    public function __construct()
    {
        $this->pageModel = new PageModel();
    }

    /**
     * Affiche une page dynamique basée sur le slug
     * 
     * @param string $slug
     * @return string
     */
    public function view(string $slug)
    {
        // Récupérer la page par son slug
        $page = $this->pageModel->getPageBySlug($slug);

        // Page non trouvée ou inactive
        if (!$page) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(
                "La page '$slug' n'existe pas."
            );
        }

        // Construire le fil d'Ariane
        $breadcrumb = $this->pageModel->getBreadcrumb($page['id']);

        // Préparer les métadonnées pour le SEO
        $pageData = [
            'page' => $page,
            'breadcrumb' => $breadcrumb,
            'title' => $page['meta_title'] ?? $page['title'],
            'description' => $page['meta_description'] ?? '',
            'meta_keywords' => $page['meta_keywords'] ?? '',
        ];

        // Fusionner avec les données de navigation du BaseController
        $data = array_merge($this->data, $pageData);

        // Charger la vue avec le template
        return view('pages/view', $data);
    }

    /**
     * Handle feature pages
     */
    public function __call($name, $arguments)
    {
        if (strpos($name, 'feature_') === 0) {
            $feature = substr($name, 8);
            return $this->view('fonctionnalites/' . $feature);
        }
        
        if (strpos($name, 'profile_') === 0) {
            $profile = substr($name, 8);
            return $this->view('pour/' . $profile);
        }

        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }
}
