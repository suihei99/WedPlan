<?php

namespace App\Http\Requests\Requests\Couple;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Expense;
use Illuminate\Validation\Rule;

class ExpenseRequest extends FormRequest
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
            'budget_category_id' => ['required', 'exists:budgetcategories,id'],
            'expense_name' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'date_paid' => ['nullable', 'date'],
            'description' => ['nullable', 'string'],
            'payment_method' => ['nullable', Rule::in(Expense::METHOD)],
        ];
    }
}
