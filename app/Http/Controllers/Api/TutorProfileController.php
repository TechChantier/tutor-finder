<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TutorProfileController extends Controller
{
    public function __construct() 
    {
        $this->middleware(['auth:sanctum', 'tutor']);
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
