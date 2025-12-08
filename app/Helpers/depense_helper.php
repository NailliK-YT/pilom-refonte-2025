<?php

/**
 * Depense Helper
 * 
 * Utility functions for expense management module
 */

if (!function_exists('format_montant')) {
    /**
     * Format amount with currency
     * 
     * @param float $montant Amount to format
     * @param string $currency Currency symbol (default: €)
     * @return string Formatted amount
     */
    function format_montant(float $montant, string $currency = '€'): string
    {
        return number_format($montant, 2, ',', ' ') . ' ' . $currency;
    }
}

if (!function_exists('get_statut_badge')) {
    /**
     * Generate HTML badge for expense status
     * 
     * @param string $statut Status: brouillon, valide, archive
     * @return string HTML badge
     */
    function get_statut_badge(string $statut): string
    {
        $badges = [
            'brouillon' => '<span class="badge badge-warning">Brouillon</span>',
            'valide' => '<span class="badge badge-success">Validé</span>',
            'archive' => '<span class="badge badge-secondary">Archivé</span>'
        ];

        return $badges[$statut] ?? '<span class="badge badge-light">' . esc($statut) . '</span>';
    }
}

if (!function_exists('get_methode_paiement_label')) {
    /**
     * Get payment method label
     * 
     * @param string $methode Payment method code
     * @return string Translated label
     */
    function get_methode_paiement_label(string $methode): string
    {
        $methodes = [
            'especes' => 'Espèces',
            'cheque' => 'Chèque',
            'virement' => 'Virement bancaire',
            'cb' => 'Carte bancaire'
        ];

        return $methodes[$methode] ?? $methode;
    }
}

if (!function_exists('get_methode_paiement_icon')) {
    /**
     * Get payment method icon
     * 
     * @param string $methode Payment method code
     * @return string Icon HTML
     */
    function get_methode_paiement_icon(string $methode): string
    {
        $icons = [
            'especes' => '<i class="fas fa-money-bill-wave"></i>',
            'cheque' => '<i class="fas fa-money-check"></i>',
            'virement' => '<i class="fas fa-exchange-alt"></i>',
            'cb' => '<i class="fas fa-credit-card"></i>'
        ];

        return $icons[$methode] ?? '<i class="fas fa-wallet"></i>';
    }
}

if (!function_exists('calculate_tva')) {
    /**
     * Calculate TVA amount
     * 
     * @param float $montantHT Amount excluding tax
     * @param float $taux TVA rate (percentage)
     * @return float TVA amount
     */
    function calculate_tva(float $montantHT, float $taux): float
    {
        return round($montantHT * ($taux / 100), 2);
    }
}

if (!function_exists('calculate_ttc')) {
    /**
     * Calculate TTC from HT and TVA rate
     * 
     * @param float $montantHT Amount excluding tax
     * @param float $taux TVA rate (percentage)
     * @return float Amount including tax
     */
    function calculate_ttc(float $montantHT, float $taux): float
    {
        return round($montantHT * (1 + $taux / 100), 2);
    }
}

if (!function_exists('validate_siret')) {
    /**
     * Validate SIRET number using Luhn algorithm
     * 
     * @param string $siret SIRET number (14 digits)
     * @return bool True if valid
     */
    function validate_siret(string $siret): bool
    {
        // Remove spaces and check length
        $siret = str_replace(' ', '', $siret);

        if (strlen($siret) !== 14 || !ctype_digit($siret)) {
            return false;
        }

        // Luhn algorithm
        $sum = 0;
        for ($i = 0; $i < 14; $i++) {
            $digit = (int) $siret[$i];

            // Double every other digit starting from position 0
            if ($i % 2 === 0) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }

            $sum += $digit;
        }

        return $sum % 10 === 0;
    }
}

if (!function_exists('allowed_file_extensions')) {
    /**
     * Get allowed file extensions for justificatifs
     * 
     * @return array Allowed extensions
     */
    function allowed_file_extensions(): array
    {
        return ['pdf', 'jpg', 'jpeg', 'png'];
    }
}

if (!function_exists('get_file_icon')) {
    /**
     * Get icon for file type
     * 
     * @param string $filename Filename
     * @return string Icon HTML
     */
    function get_file_icon(string $filename): string
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        $icons = [
            'pdf' => '<i class="fas fa-file-pdf text-danger"></i>',
            'jpg' => '<i class="fas fa-file-image text-primary"></i>',
            'jpeg' => '<i class="fas fa-file-image text-primary"></i>',
            'png' => '<i class="fas fa-file-image text-primary"></i>'
        ];

        return $icons[$extension] ?? '<i class="fas fa-file"></i>';
    }
}

if (!function_exists('format_file_size')) {
    /**
     * Format file size in human-readable format
     * 
     * @param int $bytes File size in bytes
     * @return string Formatted size
     */
    function format_file_size(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
}

if (!function_exists('get_recurrence_statut_badge')) {
    /**
     * Get badge for recurrence status
     * 
     * @param string $statut Status: actif, suspendu, termine
     * @return string HTML badge
     */
    function get_recurrence_statut_badge(string $statut): string
    {
        $badges = [
            'actif' => '<span class="badge badge-success">Actif</span>',
            'suspendu' => '<span class="badge badge-warning">Suspendu</span>',
            'termine' => '<span class="badge badge-secondary">Terminé</span>'
        ];

        return $badges[$statut] ?? '<span class="badge badge-light">' . esc($statut) . '</span>';
    }
}

if (!function_exists('format_date_fr')) {
    /**
     * Format date in French format
     * 
     * @param string $date Date string
     * @return string Formatted date
     */
    function format_date_fr(string $date): string
    {
        if (empty($date)) {
            return '';
        }

        $timestamp = strtotime($date);
        if ($timestamp === false) {
            return $date;
        }

        $months = [
            1 => 'janvier',
            'février',
            'mars',
            'avril',
            'mai',
            'juin',
            'juillet',
            'août',
            'septembre',
            'octobre',
            'novembre',
            'décembre'
        ];

        return date('j', $timestamp) . ' ' . $months[(int) date('n', $timestamp)] . ' ' . date('Y', $timestamp);
    }
}

if (!function_exists('get_justificatif_url')) {
    /**
     * Get URL for justificatif file
     * 
     * @param string|null $path Relative path to justificatif
     * @return string|null Full URL or null
     */
    function get_justificatif_url(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        return base_url('uploads/depenses/' . $path);
    }
}

if (!function_exists('get_justificatif_path')) {
    /**
     * Get absolute filesystem path for justificatif
     * 
     * @param string|null $path Relative path to justificatif
     * @return string|null Absolute path or null
     */
    function get_justificatif_path(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        return WRITEPATH . 'uploads/depenses/' . $path;
    }
}
