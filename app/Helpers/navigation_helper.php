<?php

if (!function_exists('render_menu')) {
    /**
     * Génère le HTML du menu principal (récursif pour multi-niveaux)
     * 
     * @param array $items
     * @param int $level
     * @return string
     */
    function render_menu(array $items, int $level = 0): string
    {
        if (empty($items)) {
            return '';
        }

        $html = '<ul class="menu level-' . $level . '">';

        foreach ($items as $item) {
            $hasChildren = !empty($item['children']);
            $activeClass = (current_url() === site_url($item['slug'])) ? ' active' : '';
            $label = $item['menu_label'] ?? $item['title'];

            $html .= '<li class="menu-item' . $activeClass . ($hasChildren ? ' has-children' : '') . '">';
            $html .= '<a href="' . site_url(esc($item['slug'])) . '">' . esc($label) . '</a>';

            // Sous-menu récursif
            if ($hasChildren) {
                $html .= render_menu($item['children'], $level + 1);
            }

            $html .= '</li>';
        }

        $html .= '</ul>';

        return $html;
    }
}

if (!function_exists('render_footer')) {
    /**
     * Génère le HTML du footer
     * 
     * @param array $items
     * @return string
     */
    function render_footer(array $items): string
    {
        if (empty($items)) {
            return '';
        }

        $html = '<ul class="footer-links">';

        foreach ($items as $item) {
            $html .= '<li>';
            $html .= '<a href="' . site_url(esc($item['slug'])) . '">' . esc($item['title']) . '</a>';
            $html .= '</li>';
        }

        $html .= '</ul>';

        return $html;
    }
}

if (!function_exists('render_breadcrumb')) {
    /**
     * Génère le fil d'Ariane
     * 
     * @param array $breadcrumb
     * @param string $separator
     * @return string
     */
    function render_breadcrumb(array $breadcrumb, string $separator = '/'): string
    {
        if (empty($breadcrumb)) {
            return '';
        }

        $html = '<nav class="breadcrumb" aria-label="Fil d\'Ariane">';
        $html .= '<ol class="breadcrumb-list">';

        $totalItems = count($breadcrumb);
        $currentIndex = 0;

        foreach ($breadcrumb as $item) {
            $currentIndex++;
            $isLast = ($currentIndex === $totalItems);

            $html .= '<li class="breadcrumb-item' . ($isLast ? ' active' : '') . '">';

            if (!$isLast) {
                $html .= '<a href="' . site_url(esc($item['slug'])) . '">' . esc($item['title']) . '</a>';
                $html .= '<span class="separator">' . esc($separator) . '</span>';
            } else {
                $html .= '<span>' . esc($item['title']) . '</span>';
            }

            $html .= '</li>';
        }

        $html .= '</ol>';
        $html .= '</nav>';

        return $html;
    }
}

if (!function_exists('current_url_matches')) {
    /**
     * Vérifie si l'URL courante correspond au slug donné
     * 
     * @param string $slug
     * @return bool
     */
    function current_url_matches(string $slug): bool
    {
        return current_url() === site_url($slug);
    }
}
