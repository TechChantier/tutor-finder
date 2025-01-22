<?php

namespace App\Http\Requests\TutorProfile;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
     public function authorize(): bool
    {
        return $this->user() && $this->user()->isTutor();
    }

    protected function failedAuthorization()
    {
        throw new \Illuminate\Auth\Access\AuthorizationException(
            'Only tutors can update their profile.'
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
       return [
            'bio' => ['required', 'string', 'min:50', 'max:1000'],
            'years_of_experience' => ['required', 'integer', 'min:0'],
            'availability_status' => ['sometimes', 'string', 'in:available,busy'],
        ];
    }

     public function messages(): array
    {
        return [
            'bio.min' => 'Your bio should be at least 50 characters to properly describe your expertise',
            'years_of_experience.min' => 'Years of experience cannot be negative',
        ];
    }
}






// <?php

// namespace App\Http\Controllers\Api;

// use App\Http\Controllers\Controller;
// use App\Http\Requests\TutorProfile\UpdateRequest;
// use App\Http\Resources\TutorProfileResource;
// use Illuminate\Http\Request;

// class TutorProfileController extends Controller
// {
//     public function __construct() 
//     {
//         $this->middleware(['auth:sanctum', 'tutor']);
//     }

//     /**
//      * Get authenticated tutor's profile
//      */
//     public function show(Request $request)
//     {
//         return new TutorProfileResource($request->user()->tutorProfile);
//     }

//     /**
//      * Update authenticated tutor's profile
//      */
//     public function update(UpdateRequest $request)
//     {
//         $profile = $request->user()->tutorProfile;
        
//         $profile->update($request->validated());

//         return new TutorProfileResource($profile->fresh());
//     }
// }

// public function signup(RegisterRequest $request): JsonResponse 
// {
//     $validatedFields = $request->validated();

//     // Create user with all validated fields
//     $user = User::create([
//         'name' => $validatedFields['name'],
//         'email' => $validatedFields['email'],
//         'password' => Hash::make($validatedFields['password']),
//         'phone_number' => $validatedFields['phone_number'],
//         'whatsapp_number' => $validatedFields['whatsapp_number'],
//         'user_type' => $validatedFields['user_type'],
//         'location' => $validatedFields['location'],
//         'profile_image' => '',
//     ]);

//     // Create tutor profile if user is registering as a tutor
//     if ($validatedFields['user_type'] === 'tutor') {
//         $user->tutorProfile()->create([
//             'verification_status' => 'pending',
//             'availability_status' => 'available',
//         ]);
//     }

//     return response()->json([
//         'message' => 'User registered successfully',
//         'user' => new UserResource($user),
//     ], 201);
// }