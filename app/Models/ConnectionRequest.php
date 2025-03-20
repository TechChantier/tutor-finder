<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConnectionRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'learner_id',
        'tutor_id',
        'message',
        'learner_whatsapp',
        'tutor_whatsapp',
        'status',
        'period_type',
        'amount_paid',
        'payment_completed',
        'start_date',
        'end_date'
    ];

    protected $casts = [
        'payment_completed' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime'
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';
    

    public function learner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'learner_id');
    }

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tutor_id');
    }
}
