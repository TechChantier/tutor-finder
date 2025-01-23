<?php

namespace App\Http\Requests\Applications;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->isTutor();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'proposal_message' => ['required', 'string', 'min:40', 'max:1000'],
        ];
    }

    protected function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->gig->applications()->where('tutor_id', auth()->id())->exists()) {
                $validator->errors()->add('gig', 'You have already applied to this gig');
            }
        });
    }


    public function messages(): array
    {
        return [
            'proposal_message.min' => 'Please provide a detailed proposal of at least 40 characters'
        ];
    }
}
