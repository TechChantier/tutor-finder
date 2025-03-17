<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gig extends Model
{
    use HasFactory;

    protected $fillable = [
        'learner_id',
        'category_id',
        'title',
        'description',
        'budget',
        'location',
        'status',
        'budget_period'
    ];
    
    public const STATUS_PENDING = 'pending';
    public const STATUS_OPEN = 'open';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    public const PERIOD_HOURLY = 'hourly';
    public const PERIOD_DAILY = 'daily';
    public const PERIOD_WEEKLY = 'weekly';
    public const PERIOD_MONTHLY = 'monthly';

    public function learner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'learner_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }
    
    // Helper method to check if gig is pending
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }
    
    // Helper method to check if gig is open
    public function isOpen(): bool
    {
        return $this->status === self::STATUS_OPEN;
    }
}