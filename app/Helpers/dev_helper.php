<?php

if (!function_exists('get_dev_pages')) {
    function get_dev_pages() {
        $controllersPath = APPPATH . 'Controllers';
        $files = scandir($controllersPath);
        $pages = [];

        foreach ($files as $file) {
            if ($file === '.' || $file === '..' || $file === 'BaseController.php' || strpos($file, '.php') === false) {
                continue;
            }

            $controllerName = str_replace('.php', '', $file);
            // Basic assumption: Index method is the main page
            $pages[$controllerName] = base_url(strtolower($controllerName));
            
            // If we wanted to be fancier, we could reflect the class to find all public methods
            // but for now, let's just link to the controller root (which usually maps to index)
        }
        
        // Manual additions if needed or specific logic
        return $pages;
    }
}
