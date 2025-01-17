<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\TutorResource;
use App\Models\Category;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index(): AnonymousResourceCollection
    {
        $categories = Category::all();
        return CategoryResource::collection($categories);
    }

    /**
     * Display the specified category.
     */
    // public function show(Category $category): CategoryResource
    // {
    //     return new CategoryResource($category);
    // }

    public function tutors(Category $category): AnonymousResourceCollection
    {
        $tutors = $category->tutors()
            ->where('user_type', 'tutor')
            ->with(['tutorProfile', 'qualifications', 'categories'])
            ->get();
        
        return TutorResource::collection($tutors);
    }
}