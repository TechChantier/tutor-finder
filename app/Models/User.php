<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable , HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'user_type',
        'location',
        'profile_image'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

      //Relationships For tutors
      /**
       * Each tutor (user) can have only ONE profile
       * usage: $user->tutorProfile->bio
       */
    public function tutorProfile(): HasOne
    {
        return $this->hasOne(TutorProfile::class);
    }

    /**
     * A tutor can have MANY qualifications and The 'tutor_id' in the qualifications table refers to the user's ID
     * usage: $user->qualifications (gets all qualifications)
     */
    public function qualifications(): HasMany
    {
        return $this->hasMany(Qualification::class, 'tutor_id');
    }
 
    /**
     * A tutor can teach MANY categories
     * A category can have MANY tutors 
     * usage:$user->categories (gets all categories they teach)
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'tutor_categories', 'tutor_id', 'category_id');
    }

    /**
     * A tutor can make MANY applications to different gigs
     * usage: $user->applications (gets all their applications)
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'tutor_id');
    }

    //Relationships For learners
    /**
     * A learner can create MANY gigs
     * Usage: $user->gigs (gets all gigs created by this learner)
     */
    public function gigs(): HasMany
    {
        return $this->hasMany(Gig::class, 'learner_id');
    }

      /**
     * Check if the user is a learner.
     *
     * @return bool
     */
    public function isLearner(): bool
    {
        return $this->user_type === 'learner';
    }

    /**
     * Check if the user is a tutor.
     *
     * @return bool
     */
    public function isTutor(): bool
    {
        return $this->user_type === 'tutor';
    }

        public function connectionRequestsAsTutor(): HasMany
    {
        return $this->hasMany(ConnectionRequest::class, 'tutor_id');
    }

    public function connectionRequestsAsLearner(): HasMany
    {
        return $this->hasMany(ConnectionRequest::class, 'learner_id');
    }
}
