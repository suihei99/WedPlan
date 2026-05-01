<?php

namespace App\Http\Requests\Requests\Couple;

use App\Models\Guest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GuestRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^(?:\+60|60|0)1\d{8,9}$/'],
            'pax_count' => ['nullable', 'integer', 'min:1'],
            'rsvp_status' => ['nullable', 'string', Rule::in(Guest::RSVP_STATUS)],
        ];
    }

    protected function prepareForValidation(): void
    {
        $phone = $this->input('phone');

        if ($phone !== null) {
            $normalizedPhone = preg_replace('/[\s\-]/', '', (string) $phone);
            $this->merge([
                'phone' => $normalizedPhone !== '' ? $normalizedPhone : null,
            ]);
        }
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'Please enter a valid Malaysia mobile number (e.g. +60123456789).',
        ];
    }
}
