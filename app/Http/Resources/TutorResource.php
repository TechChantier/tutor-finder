<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TutorResource extends JsonResource
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
            'name' => $this->name,
            'location' => $this->location,
            'whatsapp_number' => $this->whatsapp_number,
            'profile_image' => $this->profile_image,
            'tutor_profile' => [
                'bio' => $this->tutorProfile->bio,
                'years_of_experience' => $this->tutorProfile->years_of_experience,
                'verification_status' => $this->tutorProfile->verification_status,
                'availability_status' => $this->tutorProfile->availability_status,
            ],
            'qualifications' => $this->qualifications,
            'categories' => CategoryResource::collection($this->categories),
        ];
    }
}
