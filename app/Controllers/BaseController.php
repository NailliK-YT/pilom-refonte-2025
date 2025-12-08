<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\PageModel;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = ['navigation'];

    /**
     * Données communes à toutes les vues
     *
     * @var array
     */
    protected $data = [];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = service('session');
        helper('dev');

        // Charger les données de navigation communes
        $this->loadNavigationData();
    }

    /**
     * Charge les données du menu et du footer
     * Ces données seront disponibles dans toutes les vues
     */
    protected function loadNavigationData(): void
    {
        $cache = \Config\Services::cache();

        // Menu (cache pendant 1 heure)
        $menu = $cache->get('site_menu');
        if ($menu === null) {
            $pageModel = new PageModel();
            $menu = $pageModel->getMenuPages();
            $cache->save('site_menu', $menu, 3600);
        }

        // Footer (cache pendant 1 heure)
        $footer = $cache->get('site_footer');
        if ($footer === null) {
            $pageModel = new PageModel();
            $footer = $pageModel->getFooterPages();
            $cache->save('site_footer', $footer, 3600);
        }

        // Rendre disponibles dans toutes les vues
        $this->data['menu_items'] = $menu;
        $this->data['footer_items'] = $footer;
    }
}
