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
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone_number' => ['required', 'string', 'unique:users,phone_number', 'min:9', 'regex:/^[0-9+\-\s()]*$/'],
            'user_type' => ['required', 'string', 'in:tutor,learner'],
            'location' => ['required', 'string', 'max:255'],
            'profile_image' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048']
        ];
        
        // Add profile_video field for tutors only
        if ($this->input('user_type') === 'tutor') {
            $rules['profile_video'] = ['nullable', 'file', 'mimes:mp4,mov,avi', 'max:20480'];
        }
        
        return $rules;
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
            'phone_number.min' => 'Phone number must be at least 9 characters',
            'phone_number.regex' => 'Phone number must contain only digits, spaces, and the following characters: +()-',
            'profile_image.required' => 'Profile image is required',
            'profile_video.mimes' => 'Profile video must be in MP4, MOV, or AVI format',
        ];
    }
}