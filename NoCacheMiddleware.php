<?php

namespace App\Http\Middleware;

use Closure;

class NoCacheMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        
        // Add cache control headers
        $response->header('Cache-Control', 'no-store, nocache, private, max-age=0, must-revalidate');
        
        return $response;
    }
}
