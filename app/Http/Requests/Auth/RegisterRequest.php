<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone_number' => ['required', 'string', 'max:12'],
            'whatsapp_number' => ['required', 'string', 'max:12'],
            'user_type' => ['required', 'string', 'in:tutor,learner'],
            'location' => ['required', 'string', 'max:255'],
            'profile_image' => ['nullable', 'file', 'mimes:jpeg,png,jpg', 'max:2048']
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'user_type.in' => 'User type must be either tutor or learner',
            'password.confirmed' => 'Password confirmation does not match',
            'whatsapp_number.required' => 'WhatsApp number is required for communication',
        ];
    }
}