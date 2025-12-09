<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Performance Filter
 * 
 * Adds performance-related HTTP headers to responses.
 * This filter complements .htaccess caching with server-side headers.
 */
class PerformanceFilter implements FilterInterface
{
    /**
     * Run before the request is processed
     * 
     * @param RequestInterface $request
     * @param array|null $arguments
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Nothing to do before the request
        return null;
    }

    /**
     * Run after the request is processed
     * 
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param array|null $arguments
     * @return ResponseInterface
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Only add headers for HTML responses
        $contentType = $response->getHeaderLine('Content-Type');

        if (strpos($contentType, 'text/html') !== false || empty($contentType)) {
            // Add security and performance headers
            $headers = [
                // DNS prefetch for external resources
                'X-DNS-Prefetch-Control' => 'on',

                // Preload hint for critical resources
                'Link' => '<' . base_url('css/style.css') . '>; rel=preload; as=style',

                // Referrer policy for privacy
                'Referrer-Policy' => 'strict-origin-when-cross-origin',

                // Permissions policy (disable unused features)
                'Permissions-Policy' => 'accelerometer=(), camera=(), geolocation=(), gyroscope=(), magnetometer=(), microphone=(), usb=()',
            ];

            foreach ($headers as $name => $value) {
                if (!$response->hasHeader($name)) {
                    $response->setHeader($name, $value);
                }
            }
        }

        return $response;
    }
}
