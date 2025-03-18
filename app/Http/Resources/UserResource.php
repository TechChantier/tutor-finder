<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'user_type' => $this->user_type,
            'location' => $this->location,
            'profile_image' => $this->profile_image,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];

        // Add tutor profile data if user is a tutor and profile exists
        if ($this->user_type === 'tutor' && $this->tutorProfile) {
            $data['tutor_profile'] = [
                'bio' => $this->tutorProfile->bio,
                'years_of_experience' => $this->tutorProfile->years_of_experience,
                'verification_status' => $this->tutorProfile->verification_status,
                'availability_status' => $this->tutorProfile->availability_status,
                'profile_video' => $this->tutorProfile->profile_video,
            ];
        }

        return $data;
    }
}