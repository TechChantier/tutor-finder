<?php

namespace App\Http\Requests\Applications;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->isLearner() && 
               $this->route('application')->gig->learner_id === $this->user()->id;
    }

    protected function failedAuthorization()
    {
        if (!$this->user()->isLearner()) {
            throw new \Illuminate\Auth\Access\AuthorizationException(
                'Only learners can accept or decline applications.'
            );
        }

        throw new \Illuminate\Auth\Access\AuthorizationException(
            'You can only manage applications for gigs you have created.'
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
            'status' => ['required', 'string', 'in:accepted,rejected']
        ];
    }


    public function messages(): array
    {
        return [
            'status.in' => 'Status must be either accepted or rejected'
        ];
    }
}
