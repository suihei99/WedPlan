<?php

namespace App\Http\Requests\Requests\Vendor;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookingRequest extends FormRequest
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
            'couple_id' => ['required', 'integer', Rule::exists('users', 'id')->where('role', User::ROLE_COUPLE)],
            'type_service' => ['required', 'string', 'max:255'],
            'booking_date' => ['required', 'date'],
            'status' => ['sometimes', 'boolean'],
            'notes' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'couple_id.exists' => 'Please choose a valid couple account.',
            'booking_date.date' => 'Please provide a valid booking date.',
            'status.boolean' => 'Please choose a valid booking status.',
            'notes.max' => 'Notes cannot be longer than 255 characters.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'type_service' => $this->cleanString($this->input('type_service')),
            'notes' => $this->cleanNullableString($this->input('notes')),
        ]);
    }

    private function cleanString(mixed $value): ?string
    {
        $cleaned = trim((string) $value);

        return $cleaned !== '' ? $cleaned : null;
    }

    private function cleanNullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $cleaned = trim((string) $value);

        return $cleaned !== '' ? $cleaned : null;
    }
}
