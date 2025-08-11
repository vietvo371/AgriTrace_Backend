<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
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
            'full_name' => ['sometimes', 'string', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'email' => ['sometimes', 'string', 'email', 'max:255', 'unique:users,email,' . request()->route('user')],
            'password' => ['sometimes', Password::defaults()],
            'address' => ['nullable', 'string', 'max:255'],
            'profile_image' => ['nullable', 'string', 'max:255'],
            'role' => ['sometimes', 'string', 'in:farmer,cooperative'],
        ];
    }
}
