<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\ConnectionRequest;

class AcceptConnectionRequest extends FormRequest
{
    public function authorize()
    {
        $user = $this->user();
        if (!$user || !$user->isTutor()) {
            return false;
        }
        
        $connectionRequest = ConnectionRequest::find($this->route('id'));
        return $connectionRequest && $connectionRequest->tutor_id === $user->id;
    }

    public function rules()
    {
        return [
            'tutor_whatsapp' => ['required', 'string'],
        ];
    }
}
