<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Applications\StoreRequest;
use App\Http\Requests\Applications\UpdateRequest;
use App\Http\Resources\ApplicationResource;
use App\Models\Application;
use App\Models\Gig;
use Illuminate\Http\Request;

/**
 * @group Application Management
 * 
 * APIs for managing gig applications
 */
class ApplicationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * List Applications
     * 
     * Get all applications made by the authenticated tutor.
     * 
     * @authenticated
     * 
     * @response status=200 {
     *  "data": [
     *    {
     *      "id": 1,
     *      "proposal_message": "I am interested in teaching this subject...",
     *      "status": "pending",
     *      "gig": {
     *        "id": 1,
     *        "title": "Need Math Tutor",
     *        "description": "Looking for calculus tutor"
     *      },
     *      "created_at": "2025-01-23 12:00:00",
     *      "updated_at": "2025-01-23 12:00:00"
     *    }
     *  ]
     * }
     */
    public function index()
    {
        $applications = auth()->user()->applications()
            ->with(['gig', 'tutor'])
            ->get();
        
        return ApplicationResource::collection($applications);
    }

    /**
     * Apply to Gig
     * 
     * Submit an application for a specific gig.
     * 
     * @authenticated
     * @urlParam gig integer required The ID of the gig
     * @bodyParam proposal_message string required The application proposal text min:50 characters
     * 
     * @response status=201 {
     *  "data": {
     *    "id": 1,
     *    "proposal_message": "I am interested in teaching this subject...",
     *    "status": "pending",
     *    "created_at": "2025-01-23 12:00:00",
     *    "updated_at": "2025-01-23 12:00:00"
     *  }
     * }
     * 
     * @response status=400 {
     *  "message": "This gig is not open for applications"
     * }
     */
    public function apply(StoreRequest $request, Gig $gig)
    {
        if ($gig->status !== 'open') {
            return response()->json(['message' => 'This gig is not open for applications'], 400);
        }

        $application = $gig->applications()->create([
            'tutor_id' => auth()->id(),
            'proposal_message' => $request->proposal_message,
            'status' => 'pending'
        ]);

        return new ApplicationResource($application);
    }

    /**
     * Update Application Status
     * 
     * Accept or reject an application (Learner only).
     * 
     * @authenticated
     * @urlParam application integer required The ID of the application
     * @bodyParam status string required Status of application (accepted/rejected)
     * 
     * @response status=200 {
     *  "data": {
     *    "id": 1,
     *    "status": "accepted",
     *    "proposal_message": "I am interested in teaching this subject...",
     *    "created_at": "2025-01-23 12:00:00",
     *    "updated_at": "2025-01-23 12:00:00"
     *  }
     * }
     * 
     * @response status=403 {
     *  "message": "Unauthorized"
     * }
     */
    public function update(UpdateRequest $request, Application $application)
    {
        if ($application->gig->learner_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $application->update(['status' => $request->status]);
        
        return new ApplicationResource($application);
    }

    /**
     * Tutor Applications
     * 
     * Get all applications made by the authenticated tutor.
     * 
     * @authenticated
     * 
     * @response {
     *  "data": [
     *    {
     *      "id": 1,
     *      "proposal_message": "Application details...",
     *      "status": "pending",
     *      "gig": {
     *        "id": 1,
     *        "title": "Math Tutor Needed"
     *      }
     *    }
     *  ]
     * }
     */
    public function tutorApplications()
    {
        $applications = auth()->user()->applications()->with('gig')->get();
        return ApplicationResource::collection($applications);
    }

    /**
     * Gig Applications
     * 
     * Get all applications for a specific gig (Learner only).
     * 
     * @authenticated
     * @urlParam gig integer required The ID of the gig
     * 
     * @response status=200 {
     *  "data": [
     *    {
     *      "id": 1,
     *      "proposal_message": "Application details...",
     *      "status": "pending",
     *      "tutor": {
     *        "id": 1,
     *        "name": "John Doe"
     *      }
     *    }
     *  ]
     * }
     * 
     * @response status=403 {
     *  "message": "Unauthorized"
     * }
     */
    public function gigApplications(Gig $gig)
    {
        if ($gig->learner_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return ApplicationResource::collection($gig->applications);
    }
}