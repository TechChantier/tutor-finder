<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TutorResource;
use App\Http\Requests\Tutor\FilterRequest;
use App\Http\Traits\CanLoadRelationships;
use App\Models\User;
use Illuminate\Http\Request;

/**
* @group Tutors
* 
* APIs for discovering and viewing tutor profiles
*/
class TutorsController extends Controller 
{
    use CanLoadRelationships;

    private array $relations = ['tutorProfile', 'categories', 'qualifications', 'applications'];

    /**
     * List All Tutors
     * 
     * Get a list of all available tutors with optional filters.
     * 
     * @queryParam search string Search tutors by name or bio. Example: mathematics
     * @queryParam category_id integer Filter tutors by teaching category. Example: 1
     * @queryParam available string Filter by availability status (available/busy). Example: available
     * @queryParam include string Specify which relationships to include in the response. 
     *             Multiple relationships can be comma-separated.
     *             Available: tutorProfile, categories, qualifications, applications
     *             Example: include=tutorProfile,categories
     * 
     * @response {
     *  "data": [
     *    {
     *      "id": 1,
     *      "name": "John Doe",
     *      "email": "john@example.com",
     *      "phone_number": "+254700000000",
     *      "whatsapp_number": "+254700000000",
     *      "user_type": "tutor",
     *      "location": "Nairobi",
     *      "profile_image": "path/to/image.jpg"
     *    }
     *  ]
     * }
     */
    public function index(FilterRequest $request)
    {
        $query = $this->loadRelationships(User::where('user_type', 'tutor'));
        $filters = $request->validated();

        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhereHas('tutorProfile', function($q) use ($filters) {
                      $q->where('bio', 'like', '%' . $filters['search'] . '%');
                  });
            });
        }

        if (!empty($filters['category_id'])) {
            $query->whereHas('categories', function($q) use ($filters) {
                $q->where('categories.id', $filters['category_id']);
            });
        }

        if (!empty($filters['available'])) {
            $query->whereHas('tutorProfile', function($q) use ($filters) {
                $q->where('availability_status', $filters['available']);
            });
        }

        return TutorResource::collection($query->latest()->get());
    }

    /**
     * Get Tutor Details
     * 
     * Get detailed public profile information of a specific tutor.
     * 
     * @urlParam id integer required The ID of the tutor. Example: 1
     * @queryParam include string Specify which relationships to include in the response.
     *             Multiple relationships can be comma-separated.
     *             Available: tutorProfile, categories, qualifications, applications
     *             Example: include=tutorProfile,categories
     * 
     * @response {
     *  "data": {
     *    "id": 1,
     *    "name": "John Doe",
     *    "email": "john@example.com",
     *    "phone_number": "+254700000000",
     *    "whatsapp_number": "+254700000000",
     *    "user_type": "tutor",
     *    "location": "Nairobi",
     *    "profile_image": "path/to/image.jpg"
     *  }
     * }
     */
    public function show($id)
    {
        $tutor = $this->loadRelationships(
            User::where('user_type', 'tutor')->where('id', $id)
        )->firstOrFail();

        return new TutorResource($tutor);
    }
}