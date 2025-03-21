<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TutorProfile extends Model
{
     use HasFactory;

    protected $fillable = [
        'user_id',
        'bio', 
        'years_of_experience', 
        'verification_status', 
        'availability_status',
        'profile_video',
        'price_weekly',
        'price_monthly'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
