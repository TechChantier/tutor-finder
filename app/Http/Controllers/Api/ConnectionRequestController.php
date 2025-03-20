<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ConnectionRequest;
use App\Models\User;
use App\Http\Requests\CreateConnectionRequest;
use App\Http\Requests\AcceptConnectionRequest;
use App\Http\Resources\ConnectionRequestResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ConnectionRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum']);
    }

    /**
 * Get all connection requests for the authenticated user
 * 
 * Returns connection requests either sent or received by the user,
 * depending on whether they are a learner or tutor.
 * 
 * @queryParam status string Filter by request status (pending/accepted/rejected)
 * 
 * @response {
 *   "data": [
 *     {
 *       "id": 1,
 *       "learner": {
 *         "id": 2,
 *         "name": "John Doe"
 *       },
 *       "tutor": {
 *         "id": 3,
 *         "name": "Jane Smith" 
 *       },
 *       "message": "I'd like to connect for mathematics tutoring",
 *       "status": "pending",
 *       "period_type": "weekly",
 *       "amount_paid": 5000,
 *       "payment_completed": true,
 *       "created_at": "2023-06-15T14:30:00.000Z"
 *     }
 *   ]
 * }
 */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Validate filter parameters
        $filters = $request->validate([
            'status' => ['sometimes', 'string', 'in:pending,accepted,rejected'],
        ]);
        
        if ($user->isLearner()) {
            $query = $user->connectionRequestsAsLearner()
                ->with('tutor');
        } else {
            $query = $user->connectionRequestsAsTutor()
                ->with('learner');
        }
        
        // Apply status filter if provided
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        $connectionRequests = $query->latest()->get();
        
        return ConnectionRequestResource::collection($connectionRequests);
    }

     /**
     * Create Connection Request
     * 
     * Creates a new connection request from a learner to a tutor.
     * Only authenticated learners can create connection requests.
     * 
     * @bodyParam tutor_id integer required The ID of the tutor to connect with. Example: 3
     * @bodyParam message string required A message to the tutor explaining the connection request. Example: I would like help with advanced calculus topics.
     * @bodyParam learner_whatsapp string required The learner's WhatsApp number for contact purposes. Example: +1234567890
     * @bodyParam period_type string required The billing period for the connection (weekly or monthly). Example: weekly
     * 
     * @response {
     *   "data": {
     *     "id": 1,
     *     "learner": {
     *       "id": 2,
     *       "name": "John Doe"
     *     },
     *     "tutor": {
     *       "id": 3,
     *       "name": "Jane Smith" 
     *     },
     *     "message": "I would like help with advanced calculus topics",
     *     "status": "pending",
     *     "period_type": "weekly",
     *     "amount_paid": 5000,
     *     "payment_completed": false,
     *     "created_at": "2023-06-15T14:30:00.000Z"
     *   }
     * }
     * 
     * @response status=400 {
     *   "message": "You already have a pending request for this tutor."
     * }
     * 
     * @response status=400 {
     *   "message": "Selected user is not a tutor"
     * }
     * 
     * @response status=400 {
     *   "message": "Tutor has not set price for this period"
     * }
     */
    public function store(CreateConnectionRequest $request)
    {
        $validated = $request->validated();
        $user = $request->user();
        $tutor = User::findOrFail($validated['tutor_id']);

        $existingRequest = ConnectionRequest::where('learner_id', $user->id)
        ->where('tutor_id', $tutor->id)
        ->where('status', 'pending')
        ->first();

        if ($existingRequest) {
            return response()->json(['message' => 'You already have a pending request for this tutor.'], 400);
        }
        
        if (!$tutor->isTutor()) {
            return response()->json(['message' => 'Selected user is not a tutor'], 400);
        }
        
        // Get price based on period type
        $priceField = $validated['period_type'] === 'weekly' ? 'price_weekly' : 'price_monthly';
        $price = $tutor->tutorProfile->$priceField;
        
        if (!$price) {
            return response()->json(['message' => 'Tutor has not set price for this period'], 400);
        }
        
        $connectionRequest = ConnectionRequest::create([
            'learner_id' => $user->id,
            'tutor_id' => $tutor->id,
            'message' => $validated['message'],
            'learner_whatsapp' => $validated['learner_whatsapp'],
            'period_type' => $validated['period_type'],
            'amount_paid' => $price,
            'payment_completed' => false, // Initially set to false
        ]);
        
        // Send email notification to tutor
        $this->sendConnectionRequestEmail($connectionRequest);
        
        return new ConnectionRequestResource($connectionRequest);
    }


    
     /**
     * Mark Connection Request as Paid
     * 
     * Marks a connection request as paid, simulating payment completion.
     * Only the learner who created the request can mark it as paid.
     * 
     * @urlParam id integer required The ID of the connection request. Example: 1
     * 
     * @response {
     *   "data": {
     *     "id": 1,
     *     "learner": {
     *       "id": 2,
     *       "name": "John Doe"
     *     },
     *     "tutor": {
     *       "id": 3,
     *       "name": "Jane Smith" 
     *     },
     *     "message": "I would like help with advanced calculus topics",
     *     "status": "pending",
     *     "period_type": "weekly",
     *     "amount_paid": 5000,
     *     "payment_completed": true,
     *     "created_at": "2023-06-15T14:30:00.000Z"
     *   }
     * }
     * 
     * @response status=403 {
     *   "message": "Unauthorized"
     * }
     */
    public function markAsPaid(Request $request, $id)
    {
        $connectionRequest = ConnectionRequest::findOrFail($id);
        $user = $request->user();
        
        if ($connectionRequest->learner_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $connectionRequest->update([
            'payment_completed' => true
        ]);
        
        // Send email notification about payment
        $this->sendPaymentConfirmationEmail($connectionRequest);
        
        return new ConnectionRequestResource($connectionRequest);
    }
    
     /**
     * Accept Connection Request
     * 
     * Allows a tutor to accept a connection request that has been paid for.
     * Sets the connection start and end dates and updates status to accepted.
     * 
     * @urlParam id integer required The ID of the connection request. Example: 1
     * @bodyParam tutor_whatsapp string required The tutor's WhatsApp number for contact purposes. Example: +1234567890
     * 
     * @response {
     *   "data": {
     *     "id": 1,
     *     "learner": {
     *       "id": 2,
     *       "name": "John Doe"
     *     },
     *     "tutor": {
     *       "id": 3,
     *       "name": "Jane Smith" 
     *     },
     *     "message": "I would like help with advanced calculus topics",
     *     "status": "accepted",
     *     "period_type": "weekly",
     *     "amount_paid": 5000,
     *     "payment_completed": true,
     *     "tutor_whatsapp": "+1234567890",
     *     "start_date": "2023-06-15T14:30:00.000Z",
     *     "end_date": "2023-06-22T14:30:00.000Z",
     *     "created_at": "2023-06-15T14:30:00.000Z"
     *   }
     * }
     * 
     * * @response status=400 {
     *   "message": "Cannot accept request. Payment not completed."
     * }
     */

    public function accept(AcceptConnectionRequest $request, $id)
    {
        $validated = $request->validated();
        $connectionRequest = ConnectionRequest::findOrFail($id);
        
        if (!$connectionRequest->payment_completed) {
            return response()->json(['message' => 'Cannot accept request. Payment not completed.'], 400);
        }
        
        // Calculate start and end dates
        $startDate = Carbon::now();
        $endDate = $connectionRequest->period_type === 'weekly' 
            ? $startDate->copy()->addWeek() 
            : $startDate->copy()->addMonth();
        
        $connectionRequest->update([
            'status' => 'accepted',
            'tutor_whatsapp' => $validated['tutor_whatsapp'],
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);
        
        // Send email notification to learner
        $this->sendConnectionAcceptedEmail($connectionRequest);
        
        return new ConnectionRequestResource($connectionRequest);
    }
    
      /**
     * Reject Connection Request
     * 
     * Allows a tutor to reject a connection request.
     * Only the tutor who received the request can reject it.
     * 
     * @urlParam id integer required The ID of the connection request. Example: 1
     * 
     * @response {
     *   "data": {
     *     "id": 1,
     *     "learner": {
     *       "id": 2,
     *       "name": "John Doe"
     *     },
     *     "tutor": {
     *       "id": 3,
     *       "name": "Jane Smith" 
     *     },
     *     "message": "I would like help with advanced calculus topics",
     *     "status": "rejected",
     *     "period_type": "weekly",
     *     "amount_paid": 5000,
     *     "payment_completed": true,
     *     "created_at": "2023-06-15T14:30:00.000Z"
     *   }
     * }
     * 
     * @response status=403 {
     *   "message": "Unauthorized"
     * }
     */
    public function reject(Request $request, $id)
    {
        $connectionRequest = ConnectionRequest::findOrFail($id);
        $user = $request->user();
        
        if ($connectionRequest->tutor_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $connectionRequest->update([
            'status' => 'rejected'
        ]);
        
        // Send email notification to learner
        $this->sendConnectionRejectedEmail($connectionRequest);
        
        return new ConnectionRequestResource($connectionRequest);
    }
    
    /**
     * Send email when connection request is created
     */
    private function sendConnectionRequestEmail($connectionRequest)
    {
        $tutorName = $connectionRequest->tutor->name;
        $learnerName = $connectionRequest->learner->name;
        $message = $connectionRequest->message;
        $periodType = $connectionRequest->period_type;
        $amountPaid = $connectionRequest->amount_paid;
        $dashboardUrl = url('/dashboard');
    
        Mail::send('emails.connection_request_received', [
            'tutorName' => $tutorName,
            'learnerName' => $learnerName,
            'message' => $message,
            'periodType' => $periodType,
            'amountPaid' => $amountPaid,
            'dashboardUrl' => $dashboardUrl,
        ], function ($message) use ($connectionRequest) {
            $message->to($connectionRequest->tutor->email)
                    ->subject('New Connection Request');
        });
    }
    
    /**
     * Send email when payment is confirmed
     */
    private function sendPaymentConfirmationEmail($connectionRequest)
    {
        Mail::raw(
            "Hello {$connectionRequest->tutor->name},\n\n" .
            "A payment has been completed for a connection request from {$connectionRequest->learner->name}.\n" .
            "Please log in to your dashboard to accept or reject this request.",
            function ($message) use ($connectionRequest) {
                $message->to($connectionRequest->tutor->email)
                        ->subject('Payment Received for Connection Request');
            }
        );
    }
    
    /**
     * Send email when connection is accepted
     */
    private function sendConnectionAcceptedEmail($connectionRequest)
    {
        Mail::raw(
            "Hello {$connectionRequest->learner->name},\n\n" .
            "Good news! {$connectionRequest->tutor->name} has accepted your connection request.\n" .
            "WhatsApp Number: {$connectionRequest->tutor_whatsapp}\n" .
            "Your connection is valid until: {$connectionRequest->end_date->toDateString()}\n\n" .
            "You can now contact your tutor directly via WhatsApp.",
            function ($message) use ($connectionRequest) {
                $message->to($connectionRequest->learner->email)
                        ->subject('Connection Request Accepted');
            }
        );
    }
    
    /**
     * Send email when connection is rejected
     */
    private function sendConnectionRejectedEmail($connectionRequest)
    {
        Mail::raw(
            "Hello {$connectionRequest->learner->name},\n\n" .
            "We regret to inform you that {$connectionRequest->tutor->name} has declined your connection request.\n" .
            "Please login to your dashboard to find other tutors.",
            function ($message) use ($connectionRequest) {
                $message->to($connectionRequest->learner->email)
                        ->subject('Connection Request Declined');
            }
        );
    }
}