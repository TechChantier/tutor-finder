<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TutorProfileResource;
use App\Http\Requests\TutorProfile\UpdateRequest;
use App\Http\Resources\TutorResource;
use App\Models\User;
use Illuminate\Http\Request;

/**
* @group Tutor Profile Management
*
* APIs for managing tutor profiles
*/
class TutorProfileController extends Controller
{
   public function __construct() 
   {
       $this->middleware(['auth:sanctum']);
   }

   /**
    * Get Profile
    * 
    * Get authenticated tutor's profile details.
    * 
    * @authenticated
    * 
    * @response {
    *  "data": {
    *    "bio": "Experienced math tutor",
    *    "years_of_experience": 5,
    *    "verification_status": "pending",
    *    "availability_status": "available"
    *  }
    * }
    */
   public function show(Request $request)
   {
       return new TutorProfileResource($request->user()->tutorProfile);
   }

   /**
    * Update Profile
    * 
    * Update tutor profile details.
    * 
    * @authenticated
    * @bodyParam bio string required Detailed bio min:50 characters
    * @bodyParam years_of_experience integer required Years of teaching experience
    * @bodyParam availability_status string Tutor's availability (available/busy)
    * 
    * @response {
    *  "data": {
    *    "bio": "Updated tutor bio",
    *    "years_of_experience": 6,
    *    "verification_status": "pending",
    *    "availability_status": "available"
    *  }
    * }
    */
   public function update(UpdateRequest $request)
   {
       $profile = $request->user()->tutorProfile;
       $profile->update($request->validated());
       return new TutorProfileResource($profile->fresh());
   }

   /**
    * Verify Tutor
    * 
    * Update tutor's verification status.
    * 
    * @authenticated
    * @urlParam userId integer required The ID of the tutor
    * @bodyParam verification_status string required Status (verified/rejected)
    * 
    * @response {
    *  "data": {
    *    "id": 1,
    *    "name": "John Doe",
    *    "tutor_profile": {
    *      "verification_status": "verified"
    *    }
    *  }
    * }
    */
   public function verify(Request $request, $userId)
   {
       $user = User::findOrFail($userId);
       
       if (!$user->isTutor()) {
           return response()->json(['message' => 'User is not a tutor'], 404);
       }

       $request->validate([
           'verification_status' => ['required', 'in:verified,rejected']
       ]);

       $user->tutorProfile->update([
           'verification_status' => $request->verification_status
       ]);

       return new TutorResource($user);
   }
}