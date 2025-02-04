<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TutorCategory\StoreRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

/**
* @group Tutor Categories
*
* APIs for managing tutor's teaching categories
*/
class TutorCategoryController extends Controller
{
   public function __construct()
   {
       $this->middleware('auth:sanctum');
   }

   /**
    * List Teaching Categories 
    * 
    * Get all categories the authenticated tutor teaches.
    * 
    * @authenticated
    * 
    * @response {
    *  "data": [
    *    {
    *      "id": 1,
    *      "name": "Mathematics",
    *      "description": "Math tutoring"
    *    }
    *  ]
    * }
    */
   public function index(): JsonResponse
   {
       $categories = auth()->user()->categories;
       return response()->json([
           'data' => CategoryResource::collection($categories)
       ]);
   }

   /**
    * Add Teaching Categories
    * 
    * Add categories that the tutor can teach.
    * 
    * @authenticated
    * @bodyParam category_ids array required List of category IDs
    * @bodyParam category_ids.* integer required Must exist in categories table
    * 
    * @response {
    *  "message": "Categories added successfully",
    *  "data": [
    *    {
    *      "id": 1,
    *      "name": "Mathematics"
    *    }
    *  ]
    * }
    */
   public function store(StoreRequest $request): JsonResponse
   {
       $tutor = auth()->user();
       $tutor->categories()->syncWithoutDetaching($request->validated('category_ids'));

       return response()->json([
           'message' => 'Categories added successfully',
           'data' => CategoryResource::collection($tutor->categories)
       ]);
   }

   /**
    * Remove Teaching Category
    * 
    * Remove a category from tutor's teaching list.
    * 
    * @authenticated
    * @urlParam category integer required The ID of the category
    * 
    * @response {
    *  "message": "Category removed successfully"
    * }
    * 
    * @response status=404 {
    *  "message": "Category not found in your teaching list"
    * }
    */
   public function destroy(Category $category): JsonResponse
   {
       if (!auth()->user()->categories()->where('categories.id', $category->id)->exists()) {
           return response()->json([
               'message' => 'Category not found in your teaching list'
           ], 404);
       }

       auth()->user()->categories()->detach($category->id);
       
       return response()->json([
           'message' => 'Category removed successfully'
       ]);
   }
}