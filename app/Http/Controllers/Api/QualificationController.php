<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Qualification\StoreRequest;
use App\Http\Requests\Qualification\UpdateRequest;
use App\Http\Resources\QualificationResource;
use App\Models\Qualification;
use Illuminate\Http\JsonResponse;

class QualificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * List authenticated tutor's qualifications
     */
    public function index(): JsonResponse
    {
        $qualifications = auth()->user()->qualifications;
        
        return response()->json([
            'data' => QualificationResource::collection($qualifications)
        ]);
    }

    /**
     * Store new qualification for authenticated tutor
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $qualification = auth()->user()->qualifications()->create(
            $request->validated()
        );
    
        return response()->json([
            'message' => 'Qualification added successfully',
            'data' => $qualification
        ], 201);
    }

    /**
     * Update tutor's qualification
     */
    public function update(UpdateRequest $request, Qualification $qualification): JsonResponse
    {
        if ($qualification->tutor_id !== auth()->id()) {
            return response()->json([
                'message' => 'This qualification does not belong to you'
            ], 403);
        }

        $qualification->update($request->validated());

        return response()->json([
            'message' => 'Qualification updated successfully',
            'data' => new QualificationResource($qualification)
        ]);
    }

    /**
     * Delete tutor's qualification
     */
    public function destroy(Qualification $qualification): JsonResponse
    {
        if ($qualification->tutor_id !== auth()->id()) {
            return response()->json([
                'message' => 'This qualification does not belong to you'
            ], 403);
        }

        $qualification->delete();

        return response()->json([
            'message' => 'Qualification deleted successfully'
        ]);
    }
}