<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('edit students');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $studentId = $this->route('student');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('students', 'email')->ignore($studentId)],
            'phone' => ['required', 'string', 'max:20'],
            'country' => ['required', 'string', 'max:255'],
            'course' => ['nullable', 'string', 'max:255'],
            'target_university_id' => ['nullable', 'exists:universities,id'],
            'target_program_id' => [
                'nullable',
                'exists:programs,id',
                function ($attribute, $value, $fail) {
                    // If program is selected, ensure it belongs to the selected university
                    if ($value && $this->target_university_id) {
                        $program = \App\Models\Program::find($value);
                        if ($program && $program->university_id != $this->target_university_id) {
                            $fail('The selected program does not belong to the selected university.');
                        }
                    }
                },
            ],
            'passport_number' => ['nullable', 'string', 'max:50'],
            'passport_expiry_date' => ['nullable', 'date', 'after:today'],
            'ssc_year' => ['nullable', 'digits:4', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'ssc_result' => ['nullable', 'string', 'max:20'],
            'hsc_year' => ['nullable', 'digits:4', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'hsc_result' => ['nullable', 'string', 'max:20'],
            'has_ielts' => ['nullable', 'boolean'],
            'ielts_score' => ['nullable', 'string', 'max:10'],
            'status' => ['nullable', 'in:new,contacted,processing,applied,approved,rejected'],
            'account_status' => ['nullable', 'in:pending,approved,rejected'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'notes' => ['nullable', 'string'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Student name is required.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'A student with this email already exists.',
            'phone.required' => 'Phone number is required.',
            'country.required' => 'Country is required.',
            'assigned_to.exists' => 'The selected user does not exist.',
        ];
    }
}
