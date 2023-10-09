<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;
use Auth;

class PreventResponceReplyAttack
{
    public function handle($request, Closure $next)
    {
        // Get the session ID from the incoming request
        $sessionId = $request->cookie('coalrr_session');
        // Retrieve the session data from the session store
        $sessionData = Session::getId(); // Replace 'some_key' with your session data key
        // Check if the session data exists and is valid
        if ($sessionId != $sessionData) {
            // Session data is not valid, so log the user out or redirect to the login page
            Session::flush();
            Auth::logout();
            $request->session()->invalidate();
            return redirect()->route('login');
        }

        return $next($request);
    }


}
