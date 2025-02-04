<?php

use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\GigController;
use App\Http\Controllers\Api\TutorsController;
use App\Http\Controllers\Api\QualificationController;
use App\Http\Controllers\Api\TutorCategoryController;
use App\Http\Controllers\Api\TutorProfileController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
* API Routes for Tutor Finder Application
* 
* All routes are prefixed with 'api/'
* Public routes don't require authentication
* Protected routes require Bearer token authentication
*/

/**
* Default authenticated user route
*/
Route::get('/user', function (Request $request) {
   return $request->user();
})->middleware('auth:sanctum');

/**
* Authentication Routes
* These routes handle user registration and authentication
*/
Route::controller(AuthController::class)
    ->group(function () {
        Route::post('/signup', 'signup');
        Route::post('/login', 'login');
        Route::post('/logout', 'logout')
            ->middleware('auth:sanctum');
    });

/**
* Public Category Routes
* available teaching/learning categories
*/
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}/tutors', [CategoryController::class, 'tutors']);

/**
* Public Tutor Routes
* Allow viewing and searching available tutors
*/
Route::get('/tutors', [TutorsController::class, 'index']);
Route::get('/tutors/{id}', [TutorsController::class, 'show']);

/**
* Public Gig Routes
* Allow viewing of available learning opportunities
*/
Route::get('/gigs', [GigController::class, 'index']);
Route::get('/gigs/{gig}', [GigController::class, 'show']);

/**
* Protected Routes
* All routes in this group require authentication
*/
Route::middleware('auth:sanctum')->group(function () {
   /**
    * Auth Management Routes
    * Handle user profile management
    */
   Route::get('/user', [UserController::class, 'show']);
//    Route::put('/user', [UserController::class, 'update']);

   /**
    * Tutor Profile Management
    * Handle tutor profile operations
    */
   Route::get('/tutor-profile', [TutorProfileController::class, 'show']);
   Route::patch('/tutor-profile', [TutorProfileController::class, 'update']);
   Route::patch('/tutors/{userId}/verify', [TutorProfileController::class, 'verify']);
   /**
    * Protected Gig Management Routes
    * Handle creation, updating, and deletion of gigs
    */
   Route::post('/gigs', [GigController::class, 'store']);
   Route::patch('/gigs/{gig}', [GigController::class, 'update']);
   Route::delete('/gigs/{gig}', [GigController::class, 'destroy']);
   
   /**
    * Application Management Routes
    * Handle all application-related operations
    */
   Route::apiResource('applications', ApplicationController::class);
   Route::post('/gigs/{gig}/apply', [ApplicationController::class, 'apply']);

    /**
     * Tutor Categories Management
     * Handle tutor's teaching categories
     */
    Route::get('/tutor/categories', [TutorCategoryController::class, 'index']);
    Route::post('/tutor/categories', [TutorCategoryController::class, 'store']);
    Route::delete('/tutor/categories/{category}', [TutorCategoryController::class, 'destroy']);
   
   /**
    * Tutor Routes
    * Only accessible to authenticated tutors
    */
    Route::apiResource('qualifications', QualificationController::class);
    Route::get('/tutor/applications', [ApplicationController::class, 'tutorApplications']);

   /**
    * Learner Routes
    * Only accessible to authenticated learners
    */
    Route::get('/learner/gigs', [GigController::class, 'learnerGigs']);
    Route::get('/gigs/{gig}/applications', [ApplicationController::class, 'gigApplications']);
});