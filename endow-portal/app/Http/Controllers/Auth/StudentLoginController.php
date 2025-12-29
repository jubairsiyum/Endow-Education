<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentLoginController extends Controller
{
    protected $activityLogService;

    public function __construct(ActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;
    }

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

        // Check if user exists
        $user = \App\Models\User::where('email', $credentials['email'])->first();
        
        if (!$user) {
            return back()->withErrors([
                'email' => 'No account found with this email address.',
            ])->onlyInput('email');
        }

        // Check if user account is active
        if ($user->status !== 'active') {
            return back()->withErrors([
                'email' => 'Your account is not active. Please contact support.',
            ])->onlyInput('email');
        }

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            // Check if user has student role
            $user = Auth::user();
            if ($user->hasRole('Student')) {
                // Log student login
                $student = Student::where('user_id', $user->id)->first();
                if ($student) {
                    $this->activityLogService->logStudentLogin($student);
                }

                return redirect()->intended(route('student.dashboard'));
            }

            // If not student, logout and show error
            Auth::logout();
            return back()->withErrors([
                'email' => 'This account does not have student access. Please use admin login.',
            ])->onlyInput('email');
        }

        return back()->withErrors([
            'email' => 'The password you entered is incorrect.',
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
