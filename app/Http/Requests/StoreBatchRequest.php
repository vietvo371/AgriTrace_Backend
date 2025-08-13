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
            'category_id' => ['required', 'exists:categories,id'],
            'product_name' => ['required', 'string', 'max:255'],
            'weight' => ['required', 'string'],
            'variety' => ['required', 'string', 'max:255'],
            'planting_date' => ['required', 'date'],
            'harvest_date' => ['required', 'date', 'after_or_equal:planting_date'],
            'cultivation_method' => ['required', 'string', 'max:255'],
            'location' => ['required', 'array'],
            'location.latitude' => ['required', 'numeric'],
            'location.longitude' => ['required', 'numeric'],
            'farm_image' => ['required', 'image', 'max:5120'], // max 5MB
            'product_image' => ['required', 'image', 'max:5120'],
            'farmer_image' => ['nullable', 'image', 'max:5120'],
        ];
    }
}
