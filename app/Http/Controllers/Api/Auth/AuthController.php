<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
* @group Authentication
* 
* APIs for managing athentication
*/
class AuthController extends Controller
{

     /**
 * Register User
 * 
 * Create a new user account.
 * 
 * @bodyParam name string required User's full name
 * @bodyParam email string required User's email address must be unique
 * @bodyParam password string required Minimum of 8 characters
 * @bodyParam password_confirmation string required Must match password
 * @bodyParam phone_number string required Min 10 characters, valid phone number format
 * @bodyParam user_type string required Either 'tutor' or 'learner'
 * @bodyParam location string required User's location
 * @bodyParam profile_image file required Profile image (jpeg, png, jpg) max 2MB
 * @bodyParam profile_video file optional Video file for tutor profiles only (mp4, mov, avi) max 20MB
 * 
 * @response status=201 {
 *  "message": "User registered successfully",
 *  "user": {
 *    "id": 1,
 *    "name": "John Doe",
 *    "email": "john@example.com",
 *    "phone_number": "1234567890",
 *    "user_type": "tutor",
 *    "location": "New York",
 *    "profile_image": "https://example.com/storage/profile_images/image.jpg",
 *    "created_at": "2025-03-18 12:00:00",
 *    "updated_at": "2025-03-18 12:00:00",
 *    "tutor_profile": {
 *      "bio": "",
 *      "years_of_experience": 0,
 *      "verification_status": "pending",
 *      "availability_status": "available",
 *      "profile_video": "https://example.com/storage/profile_videos/video.mp4"
 *    }
 *  }
 * }
 * 
 * @response status=422 {
 *  "message": "The email has already been taken.",
 *  "errors": {
 *    "email": ["The email has already been taken."]
 *  }
 * }
 */
    public function signup(RegisterRequest $request): JsonResponse
    {
        $validatedFields = $request->validated();
        
        // Handle mandatory profile image upload
        $profileImagePath = null;
        if ($request->hasFile('profile_image')) {
            $image = $request->file('profile_image');
            $relativePath = $image->store('profile_images', 'public');
            $profileImagePath = asset('storage/' . $relativePath);
        }
        
        // Create user with validated fields (no WhatsApp number)
        $user = User::create([
            'name' => $validatedFields['name'],
            'email' => $validatedFields['email'],
            'password' => Hash::make($validatedFields['password']),
            'phone_number' => $validatedFields['phone_number'],
            'user_type' => $validatedFields['user_type'],
            'location' => $validatedFields['location'],
            'profile_image' => $profileImagePath,
        ]);
        
        // Handle tutor-specific profile creation with optional video
        if ($validatedFields['user_type'] === 'tutor') {
            $tutorProfileData = [
                'bio' => '',
                'years_of_experience' => 0,
                'verification_status' => 'pending',
                'availability_status' => 'available',
                'price_weekly'=> 0,
                'price_monthly'=> 0
            ];
            
            // Handle optional profile video for tutors
            if ($request->hasFile('profile_video')) {
                $video = $request->file('profile_video');
                $relativePath = $video->store('profile_videos', 'public');
                $tutorProfileData['profile_video'] = asset('storage/' . $relativePath);
            }
            
            $user->tutorProfile()->create($tutorProfileData);
        }
        
        return response()->json([
            'message' => 'User registered successfully',
            'user' => new UserResource($user),
        ], 201);
    }


 /**
     * User Login
     * 
     * Authenticate a user and get access token.
     * 
     * @bodyParam email string required User's email address
     * @bodyParam password string required User's password min:6
     * 
     * @response {
     *  "token": "1|example-token-string",
     *  "authenticated user": {
     *    "id": 1,
     *    "name": "John Doe",
     *    "email": "john@example.com"
     *  }
     * }
     * 
     * @response status=422 {
     *  "message": "The provided credentials are incorrect"
     * }
     */
     public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6']
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect']
            ]);
        }

        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect']
            ]);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'token' => $token ,
            'authenticated user' => new UserResource($user)
        ]);
    }

     /**
     * Logout User
     * 
     * Invalidate user's access token.
     * 
     * @authenticated
     * 
     * @response {
     *  "message": "You are Logged out Successfully"
     * }
     */
     public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'You are Logged out Successfully'
        ]);
    }
}
