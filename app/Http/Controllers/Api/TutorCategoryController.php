<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TutorCategory\StoreRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class TutorCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Get categories for authenticated tutor
     */
    public function index(): JsonResponse
    {
        $categories = auth()->user()->categories;
        return response()->json([
            'data' => CategoryResource::collection($categories)
        ]);
    }

    /**
     * Add categories for tutor
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
 * Remove a category for tutor
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