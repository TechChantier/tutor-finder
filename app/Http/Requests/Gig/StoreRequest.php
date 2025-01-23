<?php

namespace App\Http\Requests\Gig;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isLearner(); // here allowing Only learners to create gigs
    }

     /**
     * Override the failed authorization message
     */
    protected function failedAuthorization()
    {
        throw new \Illuminate\Auth\Access\AuthorizationException(
            'Only learners can create gigs. Tutors can view and apply to existing gigs.'
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
            'category_id' => ['required', 'exists:categories,id'],
            'title' => ['required', 'string', 'min:10', 'max:255'],
            'description' => ['required', 'string', 'min:50', 'max:1000'],
            'budget' => ['required', 'numeric', 'min:1000'],
            'location' => ['required', 'string', 'max:255'],
            'status' => ['sometimes', 'string', 'in:open,completed,cancelled'],
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
            'title.min' => 'The title should be at least 10 characters to properly describe your learning need',
            'description.min' => 'Please provide a detailed description of at least 50 characters',
            'budget.min' => 'The minimum budget should be at least 1000',
            'category_id.exists' => 'Please select a valid category',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'category_id' => 'category',
            'budget' => 'budget amount',
        ];
    }
}