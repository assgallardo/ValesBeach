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
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $role = auth()->user()->role;
            
            // Admin and Manager go to admin dashboard
            if ($role === 'admin' || $role === 'manager') {
                return redirect()->route('admin.dashboard');
            }
            
            // Staff goes to staff dashboard
            if ($role === 'staff') {
                return redirect()->route('staff.dashboard');
            }
            
            // All other roles (guest) go to guest dashboard
            return redirect()->route('guest.dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
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

        // Log the user in
        Auth::login($user);
        
        // Regenerate session for security
        $request->session()->regenerate();

        // Redirect to guest dashboard
        return redirect()->route('guest.dashboard')->with('success', 'Welcome! Your account has been created.');
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login')
            ->with('success', 'You have been logged out.');
    }
}
