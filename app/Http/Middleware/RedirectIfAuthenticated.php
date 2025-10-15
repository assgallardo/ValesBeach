<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return redirect($this->redirectTo());
            }
        }

        return $next($request);
    }
    
    /**
     * Get the post-authentication redirection path based on user role
     */
    protected function redirectTo()
    {
        $user = auth()->user();
        
        if (!$user) {
            return '/dashboard';
        }
        
        switch ($user->role) {
            case 'admin':
                return route('admin.dashboard');
            case 'manager':
                return route('manager.dashboard');
            case 'staff':
                return route('staff.dashboard');
            case 'guest':
                return route('guest.dashboard');
            default:
                return '/dashboard';
        }
    }
}
