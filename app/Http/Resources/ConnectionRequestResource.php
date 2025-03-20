<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ConnectionRequestResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'learner' => $this->whenLoaded('learner', function () {
                return [
                    'id' => $this->learner->id,
                    'name' => $this->learner->name,
                    'email' => $this->learner->email,
                ];
            }),
            'tutor' => $this->whenLoaded('tutor', function () {
                return [
                    'id' => $this->tutor->id,
                    'name' => $this->tutor->name,
                    'email' => $this->tutor->email,
                ];
            }),
            'message' => $this->message,
            'learner_whatsapp' => $this->learner_whatsapp,
            'tutor_whatsapp' => $this->when(
                $this->status === 'accepted', 
                $this->tutor_whatsapp
            ),
            'status' => $this->status,
            'period_type' => $this->period_type,
            'amount_paid' => $this->amount_paid,
            'payment_completed' => $this->payment_completed,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
