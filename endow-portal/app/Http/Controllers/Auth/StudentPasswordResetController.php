<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use App\Notifications\StudentPasswordResetNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Carbon\Carbon;

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
        $isStudent = false;
        try {
            $isStudent = $user->hasRole('Student');
        } catch (\Exception $e) {
            // If role checking fails, check if user has a student record
            $student = Student::where('user_id', $user->id)->first();
            $isStudent = $student ? true : false;
        }

        if (!$isStudent) {
            return back()->withErrors(['email' => 'This email is not registered as a student account.']);
        }

        // Create password reset token manually
        try {
            // Generate token
            $token = Str::random(64);

            // Get the password reset table name
            $table = config('auth.passwords.users.table', 'password_reset_tokens');

            // Delete old tokens for this email
            DB::table($table)->where('email', $user->email)->delete();

            // Insert new token
            DB::table($table)->insert([
                'email' => $user->email,
                'token' => Hash::make($token),
                'created_at' => Carbon::now()
            ]);

            // Send email notification
            try {
                $user->notify(new StudentPasswordResetNotification($token, $user->email));

                return back()->with('status', 'We have emailed your password reset link! Please check your inbox.');
            } catch (\Exception $mailError) {
                \Log::error('Email sending failed: ' . $mailError->getMessage());
                \Log::error('Email config - Host: ' . config('mail.mailers.smtp.host'));
                \Log::error('Email config - Port: ' . config('mail.mailers.smtp.port'));
                \Log::error('Email config - Username: ' . config('mail.mailers.smtp.username'));

                return back()->withErrors([
                    'email' => 'Unable to send email. Please contact support with this information: ' . substr($mailError->getMessage(), 0, 100)
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Password reset error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            return back()->withErrors([
                'email' => 'An error occurred while processing your request. Error: ' . $e->getMessage()
            ]);
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

        // Check if user exists
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'We could not find a user with that email address.']);
        }

        // Check if user has student role
        $isStudent = false;
        try {
            $isStudent = $user->hasRole('Student');
        } catch (\Exception $e) {
            // If role checking fails, check if user has a student record
            $student = Student::where('user_id', $user->id)->first();
            $isStudent = $student ? true : false;
        }

        if (!$isStudent) {
            return back()->withErrors(['email' => 'This email is not registered as a student account.']);
        }

        try {
            // Verify the token
            $table = config('auth.passwords.users.table', 'password_reset_tokens');
            $resetRecord = DB::table($table)->where('email', $request->email)->first();

            if (!$resetRecord) {
                return back()->withErrors(['email' => 'This password reset token is invalid.']);
            }

            // Check if token matches
            if (!Hash::check($request->token, $resetRecord->token)) {
                return back()->withErrors(['email' => 'This password reset token is invalid.']);
            }

            // Check if token is expired (60 minutes)
            $expireMinutes = config('auth.passwords.users.expire', 60);
            if (Carbon::parse($resetRecord->created_at)->addMinutes($expireMinutes)->isPast()) {
                return back()->withErrors(['email' => 'This password reset token has expired. Please request a new one.']);
            }

            // Update password
            $user->forceFill([
                'password' => Hash::make($request->password)
            ])->setRememberToken(Str::random(60));

            $user->save();

            // Also update password in student table if exists
            $student = Student::where('user_id', $user->id)->first();
            if ($student) {
                $student->update(['password' => Hash::make($request->password)]);
            }

            // Delete the used token
            DB::table($table)->where('email', $request->email)->delete();

            return redirect()->route('student.login')->with('status', 'Your password has been reset successfully! You can now login with your new password.');
        } catch (\Exception $e) {
            \Log::error('Password reset error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            return back()->withErrors(['email' => 'Unable to reset password. Error: ' . $e->getMessage()]);
        }
    }
}
