<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ValidateHostHeader
{
    public function handle(Request $request, Closure $next)
    {
        $expectedHost = config('app.url'); // Get the expected domain from your config
        //dd($request->getHost());
        if ($request->getHost() !== $expectedHost) {
            abort(400, 'Bad Request');
        }

        return $next($request);
    }
}
