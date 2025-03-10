<?php

namespace App\Http\Requests\Qualification;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isTutor();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
            'institution' => ['required', 'string', 'max:255'],
            'year_obtained' => ['required', 'integer', 'min:1950', 'max:' . date('Y')]
        ];
    }

    public function messages(): array
    {
        return [
            'year_obtained.min' => 'Year obtained must be after 1950',
            'year_obtained.max' => 'Year obtained cannot be in the future'
        ];
    }
}
