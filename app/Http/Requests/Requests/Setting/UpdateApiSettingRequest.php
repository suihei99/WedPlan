<?php

namespace App\Http\Requests\Requests\Setting;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateApiSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->user();

        $rules = [
            'email' => ['sometimes', 'required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user?->id)],
            'device_token' => ['sometimes', 'nullable', 'string', 'max:255'],
            'current_password' => ['required_with:password', 'string'],
            'password' => ['sometimes', 'required', 'string', 'min:8', 'confirmed'],
        ];

        if ($user instanceof User && $user->isVendor()) {
            $rules = array_merge($rules, [
                'business_name' => ['sometimes', 'required', 'string', 'max:255'],
                'business_type' => ['sometimes', 'required', 'string', 'max:255'],
                'contact_number' => ['sometimes', 'required', 'string', 'max:20', 'regex:/^(?:\+60|60|0)[1-9]\d{7,9}$/'],
                'address' => ['sometimes', 'required', 'string', 'max:255'],
                'profile_photo' => ['sometimes', 'nullable', 'file', 'mimes:png,webp,jpeg,jpg,gif', 'max:2048'],
                'business_documents' => ['sometimes', 'nullable', 'file', 'mimes:pdf,png', 'max:2048'],
            ]);
        }

        if ($user instanceof User && $user->isCouple()) {
            $rules = array_merge($rules, [
                'partner_1_name' => ['sometimes', 'required', 'string', 'max:255'],
                'partner_2_name' => ['sometimes', 'required', 'string', 'max:255'],
                'wedding_date' => ['sometimes', 'nullable', 'date'],
                'wedding_time' => ['sometimes', 'nullable', 'date_format:H:i'],
                'wedding_venue' => ['sometimes', 'nullable', 'string', 'max:255'],
                'total_budget_limit' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            ]);
        }

        return $rules;
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
