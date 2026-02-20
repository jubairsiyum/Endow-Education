<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use App\Notifications\AdminNewStudentRegisteredNotification;
use App\Notifications\StudentPendingApprovalNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class StudentRegisterController extends Controller
{
    /**
     * Show the student registration form.
     */
    public function showRegistrationForm()
    {
        return view('auth.student-register');
    }

    /**
     * Handle student registration request.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:students,email',
            'phone' => 'required|string|max:20',
            'gender' => 'required|in:Male,Female,Other',
            'date_of_birth' => 'required|date|before:-15 years',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $student = Student::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'gender' => $request->gender,
                'date_of_birth' => $request->date_of_birth,
                'country' => 'Bangladesh', // Default country
                'status' => 'new',
                'account_status' => 'pending',
            ]);

            // Send notifications to Super Admin, Admin, and Employee roles
            $this->notifyAdministrators($student);

            return redirect()->route('student.login')
                ->with('registration_success', 'Your registration has been submitted successfully! Please wait for account verification. You will receive an email once your account is approved.');
        } catch (\Exception $e) {
            // Log the actual error for debugging
            Log::error('Student registration failed: ' . $e->getMessage(), [
                'email' => $request->email,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Registration failed: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show registration success page.
     */
    public function success()
    {
        return view('auth.student-register-success');
    }

    /**
     * Send notifications to all administrators about new student registration.
     */
    private function notifyAdministrators(Student $student)
    {
        try {
            // Get all users with Super Admin, Admin, or Employee roles
            $administrators = User::whereHas('roles', function ($query) {
                $query->whereIn('name', ['Super Admin', 'Admin', 'Employee']);
            })
            ->where('status', 'active')
            ->get();

            // Send notification to each administrator
            foreach ($administrators as $admin) {
                try {
                    $admin->notify(new AdminNewStudentRegisteredNotification($student, $admin));
                    // Also send in-app notification for pending approval
                    if ($admin->hasRole(['Super Admin', 'Admin'])) {
                        $admin->notify(new StudentPendingApprovalNotification($student));
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to send new student registration notification to ' . $admin->email . ': ' . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to notify administrators about new student registration: ' . $e->getMessage());
        }
    }
}

