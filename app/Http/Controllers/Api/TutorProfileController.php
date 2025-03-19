<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TutorProfileResource;
use App\Http\Requests\TutorProfile\UpdateRequest;
use App\Http\Resources\TutorResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
    //     Log::info('All request data: ', $request->all());
    //    Log::info('Files array: ', $request->allFiles());
    //    Log::info('POST data: ', $_POST);
    //    Log::info('FILES data: ', $_FILES);
       
        $profile = $request->user()->tutorProfile;
        $data = $request->validated();
        
        // Debug file upload
        // Log::info('Has file: ' . $request->hasFile('profile_video'));
        
        // Handle video upload
        if ($request->hasFile('profile_video')) {
            $videoFile = $request->file('profile_video');
            // Log::info('File info: ' . $videoFile->getClientOriginalName());
            
            // Store the file in public disk
            $videoPath = $videoFile->store('profile_videos', 'public');
            // Log::info('Stored at: ' . $videoPath);
            
            $data['profile_video'] = $videoPath;
        }
        
        $profile->update($data);
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