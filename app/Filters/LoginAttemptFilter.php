<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\LoginAttemptModel;
use Config\Services;

/**
 * LoginAttemptFilter - Brute-force protection for login attempts
 * 
 * This filter intercepts POST requests to the login route and:
 * 1. Checks if the IP/email is currently blocked
 * 2. Returns a 429 response if blocked
 * 3. Records failed attempts after the controller returns an error
 */
class LoginAttemptFilter implements FilterInterface
{
    /**
     * Check if the request should be blocked before processing
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Only apply to POST requests
        if ($request->getMethod() !== 'post') {
            return;
        }

        $ip = $request->getIPAddress();
        // Cast to IncomingRequest to access getPost method
        $email = $request instanceof \CodeIgniter\HTTP\IncomingRequest
            ? $request->getPost('email')
            : null;

        $model = new LoginAttemptModel();

        // Check if blocked - single query
        if ($model->isBlocked($ip, $email)) {
            $blockedUntil = $model->getBlockedUntil($ip, $email);
            $remainingMinutes = max(1, ceil((strtotime($blockedUntil) - time()) / 60));

            // Set flash message for if they get redirected
            session()->setFlashdata('error', sprintf(
                'Trop de tentatives de connexion. Veuillez réessayer dans %d minute%s.',
                $remainingMinutes,
                $remainingMinutes > 1 ? 's' : ''
            ));

            // Return 429 Too Many Requests with the view
            return Services::response()
                ->setStatusCode(429)
                ->setBody(view('auth/rate_limited', [
                    'minutes' => $remainingMinutes,
                    'blocked_until' => $blockedUntil
                ]));
        }
        // Removed getRemainingAttempts call to speed up login
    }

    /**
     * After the request - nothing to do here
     * The controller handles recording attempts after failed login
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Recording of attempts is done in AuthController, not here,
        // because we need to know if login succeeded or failed
    }
}
