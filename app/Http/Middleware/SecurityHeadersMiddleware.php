<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $response->headers->remove('X-Powered-By');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');

        $dev = app()->environment('production')
            ? ''
            : ' http://[::1]:5173 http://localhost:5173';
        $devWs = app()->environment('production')
            ? ''
            : ' ws://[::1]:5173 ws://localhost:5173 http://[::1]:5173 http://localhost:5173';

        $cdn = 'https://cdn.jsdelivr.net https://cdnjs.cloudflare.com';
        $turnstile = 'https://challenges.cloudflare.com';

        $csp = "default-src 'self'; " .
               "script-src 'self' 'unsafe-inline' 'unsafe-eval'{$dev} {$cdn} {$turnstile}; " .
               "style-src 'self' 'unsafe-inline'{$dev} {$cdn} https://fonts.googleapis.com; " .
               "img-src 'self' data: blob: https:; " .
               "font-src 'self' data: {$cdn} https://fonts.gstatic.com; " .
               "connect-src 'self'{$devWs}; " .
               "frame-src 'self' {$turnstile}; " .
               "object-src 'none'; " .
               "base-uri 'self'; " .
               "form-action 'self';";

        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}