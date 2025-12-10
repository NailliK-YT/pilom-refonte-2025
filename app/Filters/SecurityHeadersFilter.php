<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * SecurityHeadersFilter - Applies HTTP security headers to all responses
 * 
 * This filter adds various security headers to protect against:
 * - XSS attacks
 * - Clickjacking
 * - MIME type sniffing
 * - Protocol downgrade attacks
 * - Information disclosure via referrer
 */
class SecurityHeadersFilter implements FilterInterface
{
    /**
     * Before the request - nothing to do
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Security headers are applied after the request
    }

    /**
     * After the request - apply all security headers
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Skip for CLI requests
        if (is_cli()) {
            return $response;
        }

        // ===== HTTPS ENFORCEMENT =====
        // Strict-Transport-Security (HSTS)
        // Forces browsers to use HTTPS for all future requests for 1 year
        // includeSubDomains: applies to all subdomains
        // preload: allows preloading in browser HSTS lists
        $response->setHeader(
            'Strict-Transport-Security',
            'max-age=31536000; includeSubDomains; preload'
        );

        // ===== CLICKJACKING PROTECTION =====
        // X-Frame-Options: Prevents embedding in frames/iframes on other sites
        // SAMEORIGIN: Only allow framing from same origin
        $response->setHeader('X-Frame-Options', 'SAMEORIGIN');

        // ===== MIME TYPE PROTECTION =====
        // X-Content-Type-Options: Prevents MIME type sniffing
        // Helps prevent XSS attacks via incorrect MIME type interpretation
        $response->setHeader('X-Content-Type-Options', 'nosniff');

        // ===== XSS PROTECTION (Legacy Browsers) =====
        // X-XSS-Protection: Enables XSS filter in older browsers
        // mode=block: Block page rendering if XSS is detected
        $response->setHeader('X-XSS-Protection', '1; mode=block');

        // ===== REFERRER POLICY =====
        // Controls how much referrer information is sent with requests
        // strict-origin-when-cross-origin: Full URL for same-origin, only origin for cross-origin HTTPS
        $response->setHeader('Referrer-Policy', 'strict-origin-when-cross-origin');

        // ===== CONTENT SECURITY POLICY =====
        // Comprehensive control over allowed content sources
        // Using practical CSP with 'unsafe-inline' for compatibility
        $csp = $this->buildContentSecurityPolicy();
        $response->setHeader('Content-Security-Policy', $csp);

        // ===== PERMISSIONS POLICY =====
        // Controls which browser features can be used
        // Disables features not used by Pilom to reduce attack surface
        $response->setHeader(
            'Permissions-Policy',
            'geolocation=(), microphone=(), camera=(), payment=(), usb=(), magnetometer=(), gyroscope=(), accelerometer=()'
        );

        // ===== ADDITIONAL HEADERS =====
        // X-Permitted-Cross-Domain-Policies: Controls Flash/PDF cross-domain access
        $response->setHeader('X-Permitted-Cross-Domain-Policies', 'none');

        // Prevent browser from caching sensitive pages (for authenticated pages)
        // Note: This may be too aggressive - consider applying only to authenticated routes
        // $response->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, private');
        // $response->setHeader('Pragma', 'no-cache');

        return $response;
    }

    /**
     * Build Content Security Policy header value
     * 
     * This CSP is designed to be practical while maintaining good security:
     * - Allows inline styles and scripts (for CodeIgniter views)
     * - Restricts external sources to known CDNs
     * - Prevents framing by external sites
     */
    private function buildContentSecurityPolicy(): string
    {
        $directives = [
            // Default: only from same origin
            "default-src 'self'",

            // Scripts: self, inline (for CI views), and trusted CDNs
            "script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com",

            // Styles: self, inline (for dynamic styles), and Google Fonts
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net",

            // Fonts: self and Google Fonts
            "font-src 'self' https://fonts.gstatic.com data:",

            // Images: self, data URIs (for QR codes), and HTTPS sources
            "img-src 'self' data: https: blob:",

            // Connections: self and same-origin for AJAX
            "connect-src 'self'",

            // Media: self only
            "media-src 'self'",

            // Objects: none (no plugins)
            "object-src 'none'",

            // Frames: only self (prevents clickjacking)
            "frame-ancestors 'self'",

            // Form actions: only self
            "form-action 'self'",

            // Base URI: only self (prevents base tag hijacking)
            "base-uri 'self'",

            // Upgrade insecure requests (when on HTTPS)
            "upgrade-insecure-requests",
        ];

        return implode('; ', $directives);
    }
}
