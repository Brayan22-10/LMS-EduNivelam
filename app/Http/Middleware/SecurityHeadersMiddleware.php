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
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');

        $dev = app()->environment('production')
            ? ''
            : ' http://[::1]:5173 http://localhost:5173';
        $devWs = app()->environment('production')
            ? ''
            : ' ws://[::1]:5173 ws://localhost:5173 http://[::1]:5173 http://localhost:5173';

        $cdn = 'https://cdn.jsdelivr.net https://cdnjs.cloudflare.com';
        $turnstile = 'https://challenges.cloudflare.com';
        $fonts = 'https://fonts.googleapis.com https://fonts.gstatic.com';

        $csp = "default-src 'self'; ".
               "script-src 'self'{$dev} {$cdn} {$turnstile}; ".
               "style-src 'self' 'unsafe-inline'{$dev} {$cdn} {$fonts}; ".
               "img-src 'self' data: blob: https:; ".
               "font-src 'self' data: {$cdn} {$fonts}; ".
               "connect-src 'self'{$devWs}; ".
               "frame-src 'self' {$turnstile}; ".
               "object-src 'none'; ".
               "base-uri 'self'; ".
               "form-action 'self';";

        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
