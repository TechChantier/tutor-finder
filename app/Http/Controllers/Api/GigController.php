<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Gig\StoreRequest;
use App\Http\Requests\Gig\UpdateRequest;
use App\Http\Resources\GigResource;
use App\Http\Traits\CanLoadRelationships;
use App\Models\Gig;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class GigController extends Controller
{
    use AuthorizesRequests, CanLoadRelationships;

    /**
     * Define the relationships that can be loaded
     * 
     * @var array
     */
    private array $relations = ['learner', 'category', 'applications'];

    /**
     * Create a new controller instance.
     */
    public function __construct() 
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
        $this->authorizeResource(Gig::class, 'gig');
    }

    /**
     * Display a listing of gigs.
     * 
     * @return 
     */
    public function index()
    {
        $query = $this->loadRelationships(Gig::query());
        $gigs = $query->latest()->get();
        return GigResource::collection($gigs);
    }

    /**
     * Store a newly created gig.
     * 
     * @param \App\Http\Requests\Gig\StoreRequest $request
     * @return GigResource
     */
    public function store(StoreRequest $request)
    {
        $validatedFields = $request->validated();

        $gig = Gig::create([
            'learner_id' => $request->user()->id,
            'category_id' => $validatedFields['category_id'],
            'title' => $validatedFields['title'],
            'description' => $validatedFields['description'],
            'budget' => $validatedFields['budget'],
            'location' => $validatedFields['location'],
            'status' => $validatedFields['status'] ?? Gig::STATUS_OPEN
        ]);

        return new GigResource($this->loadRelationships($gig));
    }

    /**
     * Display the specified gig.
     * 
     * @param \App\Models\Gig $gig
     * @return GigResource
     */
    public function show(Gig $gig)
    {
        return new GigResource($this->loadRelationships($gig));
    }

    /**
     * Update the specified gig.
     * 
     * @param \App\Http\Requests\Gig\UpdateRequest $request
     * @param \App\Models\Gig $gig
     * @return GigResource
     */
    public function update(UpdateRequest $request, Gig $gig)
    {
        // $this->authorize('update', $gig);
        $validatedFields = $request->validated();
        $gig->update($validatedFields);
        return new GigResource($this->loadRelationships($gig));
    }

    /**
     * Remove the specified gig.
     * 
     * @param \App\Models\Gig $gig
     * @return Response
     */
    public function destroy(Gig $gig)
    {
        $this->authorize('delete', $gig);
        $gig->delete();
        return response(status: 204);
    }
}