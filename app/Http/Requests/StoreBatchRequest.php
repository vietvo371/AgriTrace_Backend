<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBatchRequest extends FormRequest
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
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'batch_code' => ['required', 'string', 'max:255', 'unique:batches'],
            'weight' => ['required', 'numeric', 'min:0'],
            'variety' => ['nullable', 'string', 'max:255'],
            'planting_date' => ['nullable', 'date'],
            'harvest_date' => ['nullable', 'date', 'after_or_equal:planting_date'],
            'cultivation_method' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'gps_coordinates' => ['nullable', 'string', 'max:255'],
            'qr_expiry' => ['nullable', 'date', 'after:now'],
        ];
    }
}
