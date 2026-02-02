<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:account_categories,id'],
            'headline' => ['required', 'string', 'max:255'],
            'employee_id' => ['required', 'exists:users,id'],
            'currency' => ['required', 'in:BDT,USD,KRW'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'entry_date' => ['required', 'date'],
            'remarks' => ['nullable', 'string', 'max:1000'],
            'student_name' => ['nullable', 'string', 'max:255'],
            'payment_method' => ['nullable', 'string', 'max:100', 'required_if:type,income'],
            'type' => ['required', 'in:income,expense'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'category_id.required' => 'Please select a category.',
            'category_id.exists' => 'The selected category is invalid.',
            'headline.required' => 'Transaction headline is required.',
            'headline.max' => 'Transaction headline cannot exceed 255 characters.',
            'employee_id.required' => 'Please select a responsible employee.',
            'employee_id.exists' => 'The selected employee is invalid.',
            'currency.required' => 'Currency is required.',
            'currency.in' => 'Currency must be BDT, USD, or KRW.',
            'amount.required' => 'Amount is required.',
            'amount.numeric' => 'Amount must be a number.',
            'amount.min' => 'Amount must be greater than 0.',
            'entry_date.required' => 'Entry date is required.',
            'entry_date.date' => 'Entry date must be a valid date.',
            'type.required' => 'Transaction type is required.',
            'type.in' => 'Transaction type must be either income or expense.',
            'payment_method.required_if' => 'Payment method is required for income transactions.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // If type is expense, clear student_name and payment_method
        if ($this->type === 'expense') {
            $this->merge([
                'student_name' => null,
                'payment_method' => null,
            ]);
        }
    }
}
