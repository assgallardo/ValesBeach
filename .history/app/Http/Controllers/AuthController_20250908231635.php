<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLogin()
    {
        return view('login');
    }

    /**
     * Show the signup form
     */
    public function showSignup()
    {
        return view('signup');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            // Check if user account is blocked or inactive
            if (in_array($user->status, ['blocked', 'inactive'])) {
                Auth::logout();

                $message = $user->status === 'blocked'
                    ? 'Your account has been blocked. Please contact the administrator.'
                    : 'Your account has been deactivated. Please contact the administrator.';

                return back()->withErrors(['status' => $message])->withInput();
            }

            $request->session()->regenerate();

            // Redirect based on user role
            // Redirect based on user role
            return match($user->role) {
                'admin' => redirect()->intended(route('admin.dashboard')),
                'manager' => redirect()->intended(route('admin.dashboard')),
                'staff' => redirect()->intended(route('admin.dashboard')),
                'guest' => redirect()->intended(route('guest.dashboard')),
                default => redirect()->intended('/')
            };
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput();
    }

    /**
     * Handle signup request
     */
    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'terms' => 'required|accepted',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'guest',
            'status' => 'active'
        ]);

        Auth::login($user);
        
        $request->session()->regenerate();

        return redirect()->route('guest.dashboard')->with('success', 'Account created successfully!');
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
