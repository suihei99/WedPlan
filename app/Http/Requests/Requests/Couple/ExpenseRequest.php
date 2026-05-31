<?php

namespace App\Http\Requests\Requests\Couple;

use App\Models\Expense;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExpenseRequest extends FormRequest
{
    private const PAYMENT_METHOD_ALIASES = [
        'cash' => Expense::METHOD_CASH,
        'credit card' => Expense::METHOD_CREDITCARD,
        'credit_card' => Expense::METHOD_CREDITCARD,
        'debit card' => Expense::METHOD_DEBITCARD,
        'debit_card' => Expense::METHOD_DEBITCARD,
        'bank transfer' => Expense::METHOD_TRANSFER,
        'bank_transfer' => Expense::METHOD_TRANSFER,
    ];

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
            'receipt' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $paymentMethod = $this->input('payment_method');

        if (! is_string($paymentMethod)) {
            return;
        }

        $normalized = strtolower(trim($paymentMethod));

        if (! array_key_exists($normalized, self::PAYMENT_METHOD_ALIASES)) {
            return;
        }

        $this->merge([
            'payment_method' => self::PAYMENT_METHOD_ALIASES[$normalized],
        ]);
    }
}
