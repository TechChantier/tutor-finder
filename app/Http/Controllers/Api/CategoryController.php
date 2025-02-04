<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\TutorResource;
use App\Models\Category;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
* @group Categories
*
* APIs for managing teaching/learning categories
*/
class CategoryController extends Controller
{
   /**
    * List Categories
    * 
    * Get all available teaching categories.
    * 
    * @response {
    *  "data": [
    *    {
    *      "id": 1,
    *      "name": "Mathematics",
    *      "description": "Math tutoring including calculus, algebra etc."
    *    },
    *    {
    *      "id": 2, 
    *      "name": "Languages",
    *      "description": "Language tutoring including English, French etc."
    *    }
    *  ]
    * }
    */
   public function index(): AnonymousResourceCollection
   {
       $categories = Category::all();
       return CategoryResource::collection($categories);
   }

   /**
    * List Category Tutors
    * 
    * Get all tutors teaching in a specific category.
    * 
    * @urlParam category integer required The ID of the category
    * 
    * @response {
    *  "data": [
    *    {
    *      "id": 1,
    *      "name": "John Doe",
    *      "tutor_profile": {
    *        "bio": "Experienced math tutor",
    *        "years_of_experience": 5,
    *        "verification_status": "verified"
    *      },
    *      "qualifications": [
    *        {
    *          "title": "BSc Mathematics",
    *          "institution": "MIT"
    *        }
    *      ]
    *    }
    *  ]
    * }
    */
   public function tutors(Category $category): AnonymousResourceCollection
   {
       $tutors = $category->tutors()
           ->where('user_type', 'tutor')
           ->with(['tutorProfile', 'qualifications', 'categories'])
           ->get();
       
       return TutorResource::collection($tutors);
   }
}