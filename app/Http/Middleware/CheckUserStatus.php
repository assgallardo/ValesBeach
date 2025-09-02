<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Check if user is blocked or inactive
            if (in_array($user->status, ['blocked', 'inactive'])) {
                Auth::logout();
                
                $message = $user->status === 'blocked' 
                    ? 'Your account has been blocked. Please contact the administrator.'
                    : 'Your account has been deactivated. Please contact the administrator.';
                
                return redirect()->route('login')->withErrors(['status' => $message]);
            }
        }

        return $next($request);
    }
}
