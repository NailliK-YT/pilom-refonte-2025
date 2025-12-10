<?php

namespace App\Services;

use Config\Recaptcha;

/**
 * RecaptchaService
 * 
 * Handles Google reCAPTCHA v3 verification
 */
class RecaptchaService
{
    protected Recaptcha $config;

    public function __construct()
    {
        $this->config = config('Recaptcha');
    }

    /**
     * Verify a reCAPTCHA token
     * 
     * @param string $token The reCAPTCHA response token
     * @param string $action Expected action name (e.g., 'submit_comment')
     * @return array ['success' => bool, 'score' => float, 'error' => string|null]
     */
    public function verify(string $token, string $action = ''): array
    {
        // Skip if reCAPTCHA is disabled
        if (!$this->config->enabled) {
            return [
                'success' => true,
                'score' => 1.0,
                'error' => null
            ];
        }

        // Check if secret key is configured
        if (empty($this->config->secretKey)) {
            log_message('warning', 'reCAPTCHA secret key not configured');
            return [
                'success' => true, // Allow through if not configured
                'score' => 1.0,
                'error' => null
            ];
        }

        // Validate token is not empty
        if (empty($token)) {
            return [
                'success' => false,
                'score' => 0.0,
                'error' => 'Token reCAPTCHA manquant'
            ];
        }

        // Make verification request
        $client = \Config\Services::curlrequest();

        try {
            $response = $client->post($this->config->verifyUrl, [
                'form_params' => [
                    'secret' => $this->config->secretKey,
                    'response' => $token,
                    'remoteip' => service('request')->getIPAddress()
                ]
            ]);

            $result = json_decode($response->getBody(), true);

            if (!$result) {
                return [
                    'success' => false,
                    'score' => 0.0,
                    'error' => 'Réponse reCAPTCHA invalide'
                ];
            }

            // Check success
            if (!($result['success'] ?? false)) {
                $errors = $result['error-codes'] ?? ['unknown-error'];
                log_message('debug', 'reCAPTCHA failed: ' . implode(', ', $errors));
                return [
                    'success' => false,
                    'score' => 0.0,
                    'error' => 'Vérification reCAPTCHA échouée'
                ];
            }

            // Check action matches (if specified)
            if (!empty($action) && ($result['action'] ?? '') !== $action) {
                log_message('warning', 'reCAPTCHA action mismatch: expected ' . $action . ', got ' . ($result['action'] ?? 'none'));
                return [
                    'success' => false,
                    'score' => $result['score'] ?? 0.0,
                    'error' => 'Action reCAPTCHA invalide'
                ];
            }

            // Check score threshold
            $score = $result['score'] ?? 0.0;
            if ($score < $this->config->scoreThreshold) {
                log_message('info', 'reCAPTCHA score too low: ' . $score);
                return [
                    'success' => false,
                    'score' => $score,
                    'error' => 'Score de confiance trop faible'
                ];
            }

            return [
                'success' => true,
                'score' => $score,
                'error' => null
            ];

        } catch (\Exception $e) {
            log_message('error', 'reCAPTCHA verification error: ' . $e->getMessage());
            return [
                'success' => false,
                'score' => 0.0,
                'error' => 'Erreur de vérification reCAPTCHA'
            ];
        }
    }

    /**
     * Check if reCAPTCHA is enabled
     */
    public function isEnabled(): bool
    {
        return $this->config->enabled && !empty($this->config->siteKey);
    }

    /**
     * Get site key for frontend integration
     */
    public function getSiteKey(): string
    {
        return $this->config->siteKey;
    }

    /**
     * Generate the reCAPTCHA script tag for inclusion in HTML
     */
    public function getScriptTag(): string
    {
        if (!$this->isEnabled()) {
            return '';
        }

        return sprintf(
            '<script src="https://www.google.com/recaptcha/api.js?render=%s"></script>',
            htmlspecialchars($this->config->siteKey)
        );
    }

    /**
     * Generate JavaScript for executing reCAPTCHA on form submit
     */
    public function getExecuteScript(string $action, string $formId = ''): string
    {
        if (!$this->isEnabled()) {
            return '';
        }

        $siteKey = htmlspecialchars($this->config->siteKey);
        $action = htmlspecialchars($action);

        if ($formId) {
            return <<<JS
<script>
document.getElementById('{$formId}').addEventListener('submit', function(e) {
    e.preventDefault();
    var form = this;
    grecaptcha.ready(function() {
        grecaptcha.execute('{$siteKey}', {action: '{$action}'}).then(function(token) {
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'recaptcha_token';
            input.value = token;
            form.appendChild(input);
            form.submit();
        });
    });
});
</script>
JS;
        }

        return <<<JS
<script>
function executeRecaptcha(callback) {
    grecaptcha.ready(function() {
        grecaptcha.execute('{$siteKey}', {action: '{$action}'}).then(function(token) {
            callback(token);
        });
    });
}
</script>
JS;
    }
}
