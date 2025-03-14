<?php

namespace App\Http\Requests\Gig;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Checking if the user is logged in and if he is the owner of the gig
        return $this->user() && $this->gig->learner_id === $this->user()->id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => ['sometimes', 'exists:categories,id'],
            'title' => ['sometimes', 'string', 'min:10', 'max:255'],
            'description' => ['sometimes', 'string', 'min:50', 'max:1000'],
            'budget_period' => ['sometimes', 'string', 'in:hourly,daily,weekly,monthly'],
            'budget' => ['sometimes', 'numeric', 'min:1000'],
            'location' => ['sometimes', 'string', 'max:255'],
            'status' => ['sometimes', 'string', 'in:pending,open,in_progress,completed,cancelled']
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
            'title.min' => 'The title should be at least 10 characters',
            'description.min' => 'Please provide a detailed description of at least 50 characters',
            'budget.min' => 'The minimum budget should be at least 1000',
            'status.in' => 'Status must be either open, completed, or cancelled',
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
            'status' => 'gig status',
        ];
    }
}