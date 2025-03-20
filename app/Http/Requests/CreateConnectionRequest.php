<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateConnectionRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user() && $this->user()->isLearner();
    }

    public function rules()
    {
        return [
            'tutor_id' => ['required', Rule::exists('users', 'id')],
            'message' => ['required', 'string', 'min:10'],
            'learner_whatsapp' => ['required', 'string'],
            'period_type' => ['required', Rule::in(['weekly', 'monthly'])],
        ];
    }
}
