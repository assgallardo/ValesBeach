<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showSignup()
    {
        return view('auth.signup');
    }

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
            $request->session()->regenerate();
            
            $user = Auth::user();

            if (in_array($user->status, ['blocked', 'inactive'])) {
                Auth::logout();
                $message = $user->status === 'blocked'
                    ? 'Your account has been blocked. Please contact the administrator.'
                    : 'Your account has been deactivated. Please contact the administrator.';
                return back()->withErrors(['status' => $message])->withInput();
            }
            $request->session()->regenerate();

            // Redirect based on user role
            switch ($user->role) {
                case 'admin':
                    return redirect()->intended('/admin');
                case 'manager':
                case 'staff':
                default:
                    return redirect()->intended('/');
=======
            if ($user->role === 'admin' || $user->role === 'manager' || $user->role === 'staff') {
                return redirect()->route('admin.dashboard');
>>>>>>> Stashed changes
            }
            
            return redirect()->route('guest.dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput();
    }

    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
<<<<<<< Updated upstream
=======
            'role' => 'guest',
            'status' => 'active'
>>>>>>> Stashed changes
        ]);

        Auth::login($user);

        return redirect()->route('guest.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
