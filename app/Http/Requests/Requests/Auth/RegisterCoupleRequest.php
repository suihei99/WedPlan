<?php

namespace App\Http\Requests\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterCoupleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
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
    
            // couple fields
            'partner1_name' => ['required', 'string', 'max:255'],
            'partner2_name' => ['required', 'string', 'max:255'],
            'wedding_date' => ['nullable', 'date', 'after:today'],
            'wedding_location' => ['required', 'string', 'max:255'],
            'wedding_time' => ['nullable', 'date_format:H:i'],
            'total_budget_limit' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
