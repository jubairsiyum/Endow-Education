<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Normalize email: trim whitespace and convert to lowercase
        if ($this->has('email')) {
            $this->merge([
                'email' => strtolower(trim($this->input('email'))),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Get student ID from route parameter or authenticated user
        $student = $this->route('student');

        if ($student instanceof \App\Models\Student) {
            $studentId = $student->id;
            $studentModel = $student;
        } elseif ($student) {
            $studentId = $student;
            $studentModel = \App\Models\Student::find($student);
        } else {
            // If no route parameter, get from authenticated user
            $user = auth()->user();
            $studentModel = $user->student ?? \App\Models\Student::where('user_id', $user->id)->first();
            $studentId = $studentModel ? $studentModel->id : null;
        }

        // Get profile ID for student_id_number uniqueness check
        $profileId = null;
        if ($studentModel && $studentModel->profile) {
            $profileId = $studentModel->profile->id;
        }

        return [
            // Basic fields
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'required',
                'email',
                'max:255',
                Rule::unique('students', 'email')->ignore($studentId)
            ],
            'phone' => ['sometimes', 'required', 'string', 'max:20'],
            'country' => ['sometimes', 'required', 'string', 'max:100'],

            // Optional personal fields
            'surname' => ['nullable', 'string', 'max:255'],
            'given_names' => ['nullable', 'string', 'max:255'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'mother_name' => ['nullable', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'in:male,female,other'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],

            // Passport information
            'passport_number' => ['nullable', 'string', 'max:50'],
            'passport_expiry_date' => ['nullable', 'date', 'after:today'],

            // Academic fields
            'course' => ['nullable', 'string', 'max:255'],
            'target_university_id' => ['nullable', 'exists:universities,id'],
            'target_program_id' => ['nullable', 'exists:programs,id'],
            'applying_program' => ['nullable', 'string', 'max:255'],
            'highest_education' => ['nullable', 'string', 'max:255'],
            'highest_qualification' => ['nullable', 'string', 'max:255'],
            'previous_institution' => ['nullable', 'string', 'max:255'],

            // Status fields
            'status' => ['nullable', 'in:new,contacted,processing,applied,approved,rejected'],
            'account_status' => ['nullable', 'in:pending,approved,rejected'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'notes' => ['nullable', 'string'],

            // Emergency contact
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:20'],
            'emergency_contact_relationship' => ['nullable', 'string', 'max:100'],

            // SSC/HSC/IELTS Information
            'ssc_year' => ['nullable', 'string', 'max:4', 'regex:/^\d{4}$/'],
            'ssc_result' => ['nullable', 'string', 'max:20'],
            'hsc_year' => ['nullable', 'string', 'max:4', 'regex:/^\d{4}$/'],
            'hsc_result' => ['nullable', 'string', 'max:20'],
            'has_ielts' => ['nullable', 'boolean'],
            'ielts_score' => ['nullable', 'string', 'max:10'],

            // Profile data
            'profile' => ['nullable', 'array'],
            'profile.student_id_number' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('student_profiles', 'student_id_number')
                    ->ignore($profileId)
            ],
            'profile.academic_level' => ['nullable', 'string', 'max:100'],
            'profile.major' => ['nullable', 'string', 'max:255'],
            'profile.minor' => ['nullable', 'string', 'max:255'],
            'profile.gpa' => ['nullable', 'numeric', 'min:0', 'max:4'],
            'profile.enrollment_date' => ['nullable', 'date'],
            'profile.expected_graduation_date' => ['nullable', 'date', 'after:profile.enrollment_date'],
            'profile.bio' => ['nullable', 'string', 'max:1000'],
            'profile.interests' => ['nullable', 'string', 'max:500'],
            'profile.skills' => ['nullable', 'string', 'max:500'],
            'profile.languages' => ['nullable', 'array'],
            'profile.social_links' => ['nullable', 'array'],
            'profile.preferences' => ['nullable', 'array'],
            'profile.profile_notes' => ['nullable', 'string'],
        ];
    }

    /**
     * Get custom attribute names.
     */
    public function attributes(): array
    {
        return [
            'name' => 'full name',
            'date_of_birth' => 'date of birth',
            'target_university_id' => 'target university',
            'target_program_id' => 'target program',
            'ssc_year' => 'SSC year',
            'ssc_result' => 'SSC result',
            'hsc_year' => 'HSC year',
            'hsc_result' => 'HSC result',
            'has_ielts' => 'IELTS status',
            'ielts_score' => 'IELTS score',
            'profile.student_id_number' => 'student ID number',
            'profile.academic_level' => 'academic level',
            'profile.gpa' => 'GPA',
            'profile.enrollment_date' => 'enrollment date',
            'profile.expected_graduation_date' => 'expected graduation date',
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'This email address is already registered.',
            'profile.student_id_number.unique' => 'This student ID number is already in use.',
            'date_of_birth.before' => 'Date of birth must be before today.',
            'passport_expiry_date.after' => 'Passport expiry date must be in the future.',
            'profile.expected_graduation_date.after' => 'Expected graduation date must be after enrollment date.',
            'ssc_year.regex' => 'SSC year must be a valid 4-digit year.',
            'hsc_year.regex' => 'HSC year must be a valid 4-digit year.',
        ];
    }
}
