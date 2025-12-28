<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentLoginController extends Controller
{
    /**
     * Show the student login form.
     */
    public function showLoginForm()
    {
        return view('auth.student-login');
    }

    /**
     * Handle student login request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            // Check if user has student role
            $user = Auth::user();
            if ($user->hasRole('Student')) {
                return redirect()->intended(route('dashboard'));
            }

            // If not student, logout and show error
            Auth::logout();
            return back()->withErrors([
                'email' => 'This account does not have student access. Please use admin login.',
            ])->onlyInput('email');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Handle student logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('student.login');
    }
}
