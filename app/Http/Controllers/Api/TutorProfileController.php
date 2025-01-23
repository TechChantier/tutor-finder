<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TutorProfileResource;
use App\Http\Requests\TutorProfile\UpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

        return new UserResource($user);
    }

    // public function verify(Request $request, User $user)
    // {
    // Log::info('User from route model binding:', [
    //     'user' => $user,
    //     'route_parameter' => $request->route('user'),
    //     'request_path' => $request->path()
    // ]);

    // if (!$user->isTutor()) {
    //     return response()->json(['message' => 'User is not a tutor'], 404);
    // }

    // $request->validate([
    //     'verification_status' => ['required', 'in:verified,rejected']
    // ]);

    // $user->tutorProfile->update([
    //     'verification_status' => $request->verification_status
    // ]);

    // return new UserResource($user);
    // }
}
