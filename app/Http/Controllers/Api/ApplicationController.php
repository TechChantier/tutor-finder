<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Applications\StoreRequest;
use App\Http\Requests\Applications\UpdateRequest;
use App\Http\Resources\ApplicationResource;
use App\Models\Application;
use App\Models\Gig;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index()
    {
        $applications = auth()->user()->applications()
            ->with(['gig', 'tutor'])
            ->get();
        
        return ApplicationResource::collection($applications);
    }

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

    public function update(UpdateRequest $request, Application $application)
    {
        if ($application->gig->learner_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $application->update(['status' => $request->status]);
        
        return new ApplicationResource($application);
    }

    public function tutorApplications()
    {
        $applications = auth()->user()->applications()->with('gig')->get();
        return ApplicationResource::collection($applications);
    }

    public function gigApplications(Gig $gig)
    {
        if ($gig->learner_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return ApplicationResource::collection($gig->applications);
    }
}
