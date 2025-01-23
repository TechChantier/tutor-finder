<?php

namespace App\Http\Requests\TutorCategory;

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

    protected function failedAuthorization()
    {
        throw new \Illuminate\Auth\Access\AuthorizationException(
            'Only tutors can manage their teaching categories.'
        );
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_ids' => ['required', 'array'],
            'category_ids.*' => ['exists:categories,id']
        ];
    }

    public function messages(): array
    {
        return [
            'category_ids.required' => 'Please select at least one teaching category',
            'category_ids.*.exists' => 'One or more selected categories do not exist'
        ];
    }
}
