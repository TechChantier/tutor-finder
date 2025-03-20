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
            'email' => $this->email,
            'location' => $this->location,
            // 'whatsapp_number' => $this->whatsapp_number,
            'profile_image' => $this->profile_image,
            'tutor_profile' => $this->when($this->tutorProfile, [
                'bio' => $this->tutorProfile?->bio,
                'years_of_experience' => $this->tutorProfile?->years_of_experience,
                'verification_status' => $this->tutorProfile?->verification_status,
                'availability_status' => $this->tutorProfile?->availability_status,
                'profile_video' => $this->tutorProfile->profile_video,
                'price_weekly' => $this->tutorProfile->price_weekly,
                'price_monthly' => $this->tutorProfile->price_monthly,
            ]),
            'qualifications' => $this->whenLoaded('qualifications'),
            'categories' => $this->when($this->categories, 
                CategoryResource::collection($this->categories)
            ),
        ];
    }
}