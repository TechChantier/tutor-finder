<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TutorProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'bio' => $this->bio,
            'years_of_experience' => $this->years_of_experience,
            'verification_status' => $this->verification_status,
            'availability_status' => $this->availability_status,
            'profile_video' => $this->profile_video,
            'price_weekly' => $this->price_weekly,
            'price_monthly' => $this->price_monthly,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at
        ];
    }
}
