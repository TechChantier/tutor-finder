<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Qualification extends Model
{
     use HasFactory;

    protected $fillable = [
        'tutor_id',
        'title',
        'description',
        'institution',
        'year_obtained',
        'verification_status'
    ];

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tutor_id');
    }
}
