<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'budget_category_id' => $this->budget_category_id,
            'expense_name' => $this->expense_name,
            'amount' => $this->amount,
            'date_paid' => $this->date_paid,
            'description' => $this->description,
            'payment_method' => $this->payment_method,
            'receipt_path' => $this->receipt_url,
            'receipt_url' => $this->receipt_url ? asset('storage/'.$this->receipt_url) : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
