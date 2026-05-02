<?php

namespace App\Http\Requests\Requests\Vendor;

use App\Models\Service;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'service_name' => ['required', 'string', 'max:120'],
            'type_service' => ['required', 'string', Rule::in(Service::SERVICE_TYPES)],
            'price_estimate' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'description' => ['nullable', 'string', 'max:1000'],
            'image_url' => [
                $this->isMethod('post') ? 'required' : 'nullable',
                'file',
                'mimes:png,webp,jpeg,jpg,gif',
                'max:2048',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'type_service.in' => 'Please choose a wedding service category from the list.',
            'price_estimate.numeric' => 'Please enter a valid service price.',
            'image_url.required' => 'Please upload a service image.',
            'image_url.file' => 'The service image must be a valid file upload.',
            'image_url.mimes' => 'The service image must be a PNG, WEBP, JPEG, JPG, or GIF file.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'service_name' => $this->cleanString($this->input('service_name')),
            'type_service' => $this->cleanString($this->input('type_service')),
            'description' => $this->cleanNullableString($this->input('description')),
            'image_url' => $this->cleanNullableString($this->input('image_url')),
            'price_estimate' => $this->cleanPrice($this->input('price_estimate')),
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

    private function cleanPrice(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = preg_replace('/[^0-9.]/', '', (string) $value);

        return $normalized !== '' ? $normalized : null;
    }
}
