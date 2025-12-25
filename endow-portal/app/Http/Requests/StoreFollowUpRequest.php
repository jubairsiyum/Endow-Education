<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFollowUpRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create follow-ups');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'student_id' => ['required', 'exists:students,id'],
            'note' => ['required', 'string'],
            'next_follow_up_date' => ['nullable', 'date', 'after_or_equal:today'],
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
            'student_id.required' => 'Student is required.',
            'student_id.exists' => 'The selected student does not exist.',
            'note.required' => 'Follow-up note is required.',
            'next_follow_up_date.after_or_equal' => 'Follow-up date must be today or a future date.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Sanitize HTML content to prevent XSS
        if ($this->has('note')) {
            $this->merge([
                'note' => strip_tags($this->note, '<p><br><strong><em><ul><ol><li><a>'),
            ]);
        }
    }
}
