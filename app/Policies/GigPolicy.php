<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Gig;
use App\Models\User;

class GigPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Gig $gig): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isLearner();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Gig $gig): bool
    {
        return $user->id === $gig->learner_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Gig $gig): bool
    {
        return $user->id === $gig->learner_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Gig $gig): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Gig $gig): bool
    {
        return false;
    }

    /**
     * Determine whether the user can publish the gig.
     */
    public function publish(User $user, Gig $gig): bool
    {
        // Only the gig owner can publish their gig
        return $user->id === $gig->learner_id;
    }

    /**
     * Determine whether the user can unpublish the gig.
     */
    public function unpublish(User $user, Gig $gig): bool
    {
        // Only the gig owner can unpublish their gig
        return $user->id === $gig->learner_id;
    }
}
