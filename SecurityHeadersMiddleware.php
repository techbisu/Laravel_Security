<?php

namespace App\Http\Middleware;

use Closure;

class SecurityHeadersMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // Set Content Security Policy (CSP)
        $response->headers->set(
            'Content-Security-Policy',
            "default-src 'self'; img-src 'self'; style-src 'self'; script-src 'self'; connect-src 'self'; frame-src 'self';"
        );    

        // Set X-Content-Type-Options
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Set X-Frame-Options
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Set X-XSS-Protection
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        
        //$response->headers->set('Strict-Transport-Security','max-age=31536000; includeSubDomains; preload');

        return $this->addHSTSHeader($response);
    }

    private function addHSTSHeader($response)
    {
        // Add Strict Transport Security (HSTS) header
        if (config('app.env') === 'production') {
            $maxAge = 31536000; // 1 year
            $includeSubDomains = true;

            $hstsValue = "max-age=$maxAge";
            if ($includeSubDomains) {
                $hstsValue .= '; includeSubDomains';
            }

            $response->headers->set('Strict-Transport-Security', $hstsValue);
        }

        return $response;
    }
}
