<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

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

            return redirect()->route('student.registration.success')
                ->with('success', 'Your registration has been submitted successfully! You will receive an email once your account is verified.');
        } catch (\Exception $e) {
            // Log the actual error for debugging
            \Log::error('Student registration failed: ' . $e->getMessage(), [
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
}

