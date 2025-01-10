<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
     use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description'
    ];

    public function gigs(): HasMany
    {
        return $this->hasMany(Gig::class);
    }

    public function tutors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'tutor_categories', 'category_id', 'tutor_id');
    }
}
