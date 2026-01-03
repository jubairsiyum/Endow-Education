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
            'surname' => 'required|string|max:255',
            'given_names' => 'nullable|string|max:255',
            'father_name' => 'required|string|max:255',
            'mother_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:students,email',
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'required|date|before:today',
            'passport_number' => 'nullable|string|max:50',
            'nationality' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            // Academic fields removed - will be filled by counselor during approval
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
                'surname' => $request->surname,
                'given_names' => $request->given_names,
                'father_name' => $request->father_name,
                'mother_name' => $request->mother_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'passport_number' => $request->passport_number,
                'nationality' => $request->nationality,
                'country' => $request->country,
                'address' => $request->address,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
                // Academic fields will be filled by counselor during approval
                'status' => 'new',
                'account_status' => 'pending',
            ]);

            return redirect()->route('student.registration.success')
                ->with('success', 'Your registration has been submitted successfully! You will receive an email once your account is verified.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred during registration. Please try again.')
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

