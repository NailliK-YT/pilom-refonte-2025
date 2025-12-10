<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Google reCAPTCHA v3 Configuration
 */
class Recaptcha extends BaseConfig
{
    /**
     * reCAPTCHA Site Key (public)
     * Get from: https://www.google.com/recaptcha/admin
     */
    public string $siteKey = '';

    /**
     * reCAPTCHA Secret Key (private)
     */
    public string $secretKey = '';

    /**
     * Minimum score threshold (0.0 to 1.0)
     * 1.0 is very likely a good interaction, 0.0 is very likely a bot
     * Recommended: 0.5 for most cases
     */
    public float $scoreThreshold = 0.5;

    /**
     * Enable/disable reCAPTCHA globally
     */
    public bool $enabled = true;

    /**
     * reCAPTCHA v3 API endpoint
     */
    public string $verifyUrl = 'https://www.google.com/recaptcha/api/siteverify';

    public function __construct()
    {
        parent::__construct();

        // Load from environment variables if available
        $this->siteKey = env('RECAPTCHA_SITE_KEY', $this->siteKey);
        $this->secretKey = env('RECAPTCHA_SECRET_KEY', $this->secretKey);
        $this->enabled = env('RECAPTCHA_ENABLED', $this->enabled);
    }
}
