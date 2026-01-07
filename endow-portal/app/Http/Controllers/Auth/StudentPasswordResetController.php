<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

class StudentPasswordResetController extends Controller
{
    /**
     * Display the password reset request form.
     */
    public function showLinkRequestForm()
    {
        return view('auth.student-forgot-password');
    }

    /**
     * Send a reset link to the given user.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Check if user exists
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'We could not find an account with that email address.']);
        }

        // Check if user has student role (safer check)
        try {
            $isStudent = $user->hasRole('Student');
            if (!$isStudent) {
                return back()->withErrors(['email' => 'This email is not registered as a student account.']);
            }
        } catch (\Exception $e) {
            // If role checking fails, check if user has a student record
            $student = Student::where('user_id', $user->id)->first();
            if (!$student) {
                return back()->withErrors(['email' => 'This email is not registered as a student account.']);
            }
        }

        // Send password reset link
        try {
            $status = Password::sendResetLink(
                $request->only('email')
            );

            return $status === Password::RESET_LINK_SENT
                ? back()->with('status', __($status))
                : back()->withErrors(['email' => __($status)]);
        } catch (\Exception $e) {
            \Log::error('Password reset error: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Unable to send password reset email. Please try again or contact support.']);
        }
    }

    /**
     * Display the password reset form.
     */
    public function showResetForm(Request $request, $token)
    {
        return view('auth.student-reset-password', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    /**
     * Reset the given user's password.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ]);

        // Check if user has student role
        $user = User::where('email', $request->email)->first();
        if ($user) {
            try {
                $isStudent = $user->hasRole('Student');
                if (!$isStudent) {
                    return back()->withErrors(['email' => 'This email is not registered as a student account.']);
                }
            } catch (\Exception $e) {
                // If role checking fails, check if user has a student record
                $student = Student::where('user_id', $user->id)->first();
                if (!$student) {
                    return back()->withErrors(['email' => 'This email is not registered as a student account.']);
                }
            }
        }

        try {
            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    $user->forceFill([
                        'password' => Hash::make($password)
                    ])->setRememberToken(Str::random(60));

                    $user->save();

                    // Also update password in student table if exists
                    $student = Student::where('user_id', $user->id)->first();
                    if ($student) {
                        $student->update(['password' => Hash::make($password)]);
                    }
                }
            );

            return $status === Password::PASSWORD_RESET
                ? redirect()->route('student.login')->with('status', __($status))
                : back()->withErrors(['email' => [__($status)]]);
        } catch (\Exception $e) {
            \Log::error('Password reset error: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Unable to reset password. Please try again or contact support.']);
        }
    }
}
