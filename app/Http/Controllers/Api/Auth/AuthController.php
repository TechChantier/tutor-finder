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
     * @bodyParam phone_number string required Max 12 characters
     * @bodyParam whatsapp_number string required Max 12 characters
     * @bodyParam user_type string required Either 'tutor' or 'learner'
     * @bodyParam location string required User's location
     * 
     * @response status=201 {
     *  "message": "User registered successfully",
     *  "user": {
     *    "id": 1,
     *    "name": "John Doe",
     *    "email": "john@example.com",
     *    "phone_number": "1234567890",
     *    "whatsapp_number": "1234567890",
     *    "user_type": "tutor",
     *    "location": "New York",
     *    "tutor_profile": {
     *      "verification_status": "pending",
     *      "availability_status": "available"
     *    }
     *  }
     * }
     * 
     * @response status=422 {
     *  "message": "The email has already been taken"
     * }
     */
    public function signup(RegisterRequest $request): JsonResponse 
    {
        $validatedFields = $request->validated();

        // Create user with all validated fields
        $user = User::create([
            'name' => $validatedFields['name'],
            'email' => $validatedFields['email'],
            'password' => Hash::make($validatedFields['password']),
            'phone_number' => $validatedFields['phone_number'],
            'whatsapp_number' => $validatedFields['whatsapp_number'],
            'user_type' => $validatedFields['user_type'],
            'location' => $validatedFields['location'],
            'profile_image' => $validatedFields['profile_image'],
        ]);


        if ($validatedFields['user_type'] === 'tutor') {
            $user->tutorProfile()->create([
                'bio' => '',
                'years_of_experience' => 0,
                'verification_status' => 'pending',
                'availability_status' => 'available',
            ]);
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
