<?php

namespace App\Http\Requests\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterVendorRequest extends FormRequest
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
            // user fields
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed', Password::defaults()],

            // vendor fields
            'business_name' => ['required', 'string', 'max:255'],
            'business_type' => ['required', 'string', 'max:255'],
            'contact_number' => ['required', 'string', 'max:20', 'regex:/^(?:\+60|60|0)[1-9]\d{7,9}$/'],
            'address' => ['required', 'string'],
            'business_documents' => ['required', 'file', 'mimes:pdf,png', 'max:2048'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $contactNumber = $this->input('contact_number');

        if ($contactNumber !== null) {
            $normalized = preg_replace('/[\s\-]/', '', (string) $contactNumber);

            $this->merge([
                'contact_number' => $normalized,
            ]);
        }
    }

    public function messages(): array
    {
        return [
            'contact_number.regex' => 'Please enter a valid Malaysia number (e.g. +60123456789).',
            'business_documents.mimes' => 'Business document must be a PDF or PNG file.',
        ];
    }
}
