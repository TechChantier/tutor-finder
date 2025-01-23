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


class AuthController extends Controller
{
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
        'profile_image' => '',
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
        // 'token' => $token
    ], 201);
}

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

     public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'You are Logged out Successfully'
        ]);
    }
}
