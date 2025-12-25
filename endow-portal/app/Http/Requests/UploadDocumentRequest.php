<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadDocumentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('upload documents');
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
            'checklist_item_id' => ['required', 'exists:checklist_items,id'],
            'document' => [
                'required',
                'file',
                'mimes:pdf',
                'max:10240', // 10 MB in kilobytes
            ],
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
            'checklist_item_id.required' => 'Checklist item is required.',
            'checklist_item_id.exists' => 'The selected checklist item does not exist.',
            'document.required' => 'Please select a document to upload.',
            'document.file' => 'The uploaded file is invalid.',
            'document.mimes' => 'Only PDF files are allowed.',
            'document.max' => 'The document size must not exceed 10 MB.',
        ];
    }
}
