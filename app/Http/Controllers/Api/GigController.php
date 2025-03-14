<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Gig\StoreRequest;
use App\Http\Requests\Gig\UpdateRequest;
use App\Http\Resources\GigResource;
use App\Http\Traits\CanLoadRelationships;
use App\Models\Gig;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
* @group Gigs
* 
* APIs for managing learning gigs
*/
class GigController extends Controller 
{
   use AuthorizesRequests, CanLoadRelationships;

   private array $relations = ['learner', 'category', 'applications'];

   public function __construct()
   {
       $this->middleware('auth:sanctum')->except(['index', 'show']);
       $this->authorizeResource(Gig::class, 'gig');
   }

    /**
     * List All Gigs
     * 
     * Get a list of all available learning gigs. By default, pending gigs are excluded.
     * 
     * @queryParam include string Specify which relationships to include in the response. 
     *             Multiple relationships can be comma-separated.
     *             Available relationships: learner, category, applications
     * @queryParam search string Search in gig title and description
     * @queryParam price string Filter by price range (format: 1000-5000)
     * @queryParam budget_period string Filter by budget period (hourly/daily/weekly/monthly)
     * @queryParam category_id integer Filter gigs by category
     * @queryParam status string Filter by gig status (pending/open/in_progress/completed/cancelled)
     * @queryParam learner_id integer Filter gigs by the learner who created them
     * @queryParam include_pending boolean Include pending gigs (default: false)
     * 
     * @response {
     *  "data": [
     *    {
     *      "id": 1,
     *      "title": "Need Math Tutor",
     *      "description": "Looking for calculus tutor",
     *      "budget": 5000,
     *      "budget_period": "monthly",
     *      "location": "Online",
     *      "status": "open",
     *      "learner": {
     *        "id": 1,
     *        "name": "John Student"
     *      },
     *      "category": {
     *        "id": 1,
     *        "name": "Mathematics"
     *      }
     *    }
     *  ]
     * }
     */
    public function index(Request $request)
    {
        $query = $this->loadRelationships(Gig::query());

        // Validate filter parameters
        $filters = $request->validate([
            'search' => ['sometimes','string','min:3'],
            'price' => ['sometimes','string','regex:/^\d+-\d+$/'],
            'budget_period' => ['sometimes','string','in:hourly,daily,weekly,monthly'],
            'category_id' => ['sometimes','exists:categories,id'],
            'status' => ['sometimes','in:pending,open,in_progress,completed,cancelled'],
            'learner_id' => ['sometimes','exists:users,id'],
            'include_pending' => ['sometimes','boolean']
        ]);

        // By default, exclude pending gigs unless explicitly requested
        if (empty($filters['status'])) {
            if (empty($filters['include_pending']) || $filters['include_pending'] === false) {
                $query->where('status', '!=', Gig::STATUS_PENDING);
            }
        }

        // Apply search filter
        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }

        // Apply price range filter
        if (!empty($filters['price'])) {
            [$minPrice, $maxPrice] = explode('-', $filters['price']);
            $query->whereBetween('budget', [(int)$minPrice, (int)$maxPrice]);
        }

        // Apply budget period filter
        if (!empty($filters['budget_period'])) {
            $query->where('budget_period', $filters['budget_period']);
        }

        // Apply category filter
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        // Apply status filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        // Apply learner filter
        if (!empty($filters['learner_id'])) {
            $user = User::findOrFail($filters['learner_id']);
            
            // Optional: Check if user is a learner
            if (!$user->isLearner()) {
                return response()->json([
                    'message' => 'The specified user is not a learner'
                ], 400);
            }
            
            $query->where('learner_id', $filters['learner_id']);
        }

        $gigs = $query->latest()->get();
        return GigResource::collection($gigs);
    }

   /**
    * Create Gig
    * 
    * Create a new gig in pending status (Learner only). The gig will not be visible to others
    * until it is published.
    * 
    * @authenticated
    * @bodyParam title string required Gig title min:10 characters
    * @bodyParam description string required Detailed description min:50 characters
    * @bodyParam category_id integer required Valid category ID
    * @bodyParam budget numeric required Minimum amount 1000
    * @bodyParam budget_period string required Budget time period (hourly/daily/weekly/monthly)
    * @bodyParam location string required Preferred learning location
    * @bodyParam status string Status of the gig (defaults to pending)
    * 
    * @response status=201 {
    *  "data": {
    *    "id": 1,
    *    "title": "Need Math Tutor",
    *    "description": "Looking for calculus tutor...",
    *    "budget": 5000,
    *    "budget_period": "monthly",
    *    "location": "Online",
    *    "status": "pending"
    *  }
    * }
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
           'budget_period' => $validatedFields['budget_period'],
           'location' => $validatedFields['location'],
           'status' => $validatedFields['status'] ?? Gig::STATUS_PENDING
       ]);

       return new GigResource($this->loadRelationships($gig));
   }

   /**
    * Get Gig Details
    * 
    * Get detailed information about a specific gig.
    * 
    * @urlParam gig integer required The ID of the gig
    * @queryParam include string Relationships to include (learner,category,applications)
    * 
    * @response {
    *  "data": {
    *    "id": 1,
    *    "title": "Need Math Tutor",
    *    "description": "Looking for calculus tutor",
    *    "budget": 5000,
    *    "budget_period": "monthly",
    *    "location": "Online",
    *    "status": "pending",
    *    "learner": {
    *      "id": 1,
    *      "name": "John Student"  
    *    }
    *  }
    * }
    */
   public function show(Gig $gig)
   {
       return new GigResource($this->loadRelationships($gig));
   }

   /**
    * Update Gig
    * 
    * Update an existing gig (Gig owner only).
    * 
    * @authenticated
    * @urlParam gig integer required The ID of the gig
    * @bodyParam title string required Gig title min:10 characters
    * @bodyParam description string required Detailed description min:50 characters
    * @bodyParam category_id integer required Valid category ID
    * @bodyParam budget numeric required Minimum amount 1000
    * @bodyParam budget_period string required Budget time period (hourly/daily/weekly/monthly)
    * @bodyParam location string required Preferred learning location
    * @bodyParam status string Status of the gig (pending/open/completed/cancelled)
    * 
    * @response {
    *  "data": {
    *    "id": 1,
    *    "title": "Updated Math Tutor",
    *    "description": "Updated description...",
    *    "budget": 6000,
    *    "budget_period": "weekly",
    *    "location": "Online",
    *    "status": "pending"
    *  }
    * }
    */
   public function update(UpdateRequest $request, Gig $gig)  
   {
       $validatedFields = $request->validated();
       $gig->update($validatedFields);
       return new GigResource($this->loadRelationships($gig));
   }

   /**
    * Delete Gig
    * 
    * Delete a gig (Gig owner only).
    * 
    * @authenticated
    * @urlParam gig integer required The ID of the gig
    * 
    * @response status=204 {}
    */
   public function destroy(Gig $gig)
   {
       $gig->delete();
       return response(status: 204);
   }
   
   /**
    * Publish Gig
    * 
    * Change a gig's status from pending to open, making it publicly available (Gig owner only).
    * 
    * @authenticated
    * @urlParam gig integer required The ID of the gig
    * 
    * @response {
    *  "data": {
    *    "id": 1,
    *    "title": "Need Math Tutor",
    *    "description": "Looking for calculus tutor...",
    *    "budget": 5000,
    *    "budget_period": "monthly",
    *    "location": "Online",
    *    "status": "open"
    *  }
    * }
    */
   public function publish(Gig $gig)
   {
       $this->authorize('publish', $gig);
       
       // Only allow publishing if the gig is pending
       if ($gig->status !== Gig::STATUS_PENDING) {
           return response()->json([
               'message' => 'Only pending gigs can be published'
           ], 400);
       }
       
       $gig->status = Gig::STATUS_OPEN;
      $gig->save();
      
      return new GigResource($this->loadRelationships($gig));
  }
  
  /**
   * Unpublish Gig
   * 
   * Change a gig's status from open to pending, making it private (Gig owner only).
   * 
   * @authenticated
   * @urlParam gig integer required The ID of the gig
   * 
   * @response {
   *  "data": {
   *    "id": 1,
   *    "title": "Need Math Tutor",
   *    "description": "Looking for calculus tutor...",
   *    "budget": 5000,
   *    "budget_period": "monthly",
   *    "location": "Online",
   *    "status": "pending"
   *  }
   * }
   */
  public function unpublish(Gig $gig)
  {
      $this->authorize('unpublish', $gig);
      
      // Only allow unpublishing if the gig is open
      if ($gig->status !== Gig::STATUS_OPEN) {
          return response()->json([
              'message' => 'Only open gigs can be unpublished'
          ], 400);
      }
      
      $gig->status = Gig::STATUS_PENDING;
      $gig->save();
      
      return new GigResource($this->loadRelationships($gig));
  }
}