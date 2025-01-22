<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TutorProfileResource;
use App\Http\Requests\TutorProfile\UpdateRequest;
use Illuminate\Http\Request;


class TutorProfileController extends Controller
{
    public function __construct() 
    {
        $this->middleware(['auth:sanctum']);
    }

    /**
     * Get authenticated tutor's profile
     */
    public function show(Request $request)
    {
        return new TutorProfileResource($request->user()->tutorProfile);
    }

    /**
     * Update authenticated tutor's profile
     */
    public function update(UpdateRequest $request)
    {
        $profile = $request->user()->tutorProfile;
        
        $profile->update($request->validated());

        return new TutorProfileResource($profile->fresh());
    }
}
