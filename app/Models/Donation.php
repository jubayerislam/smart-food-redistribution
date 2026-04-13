<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    protected $fillable = [
        'food_category',
        'quantity',
        'quantity_kg',
        'expiry_time',
        'location',
        'special_instructions',
        'status',
        'is_hidden',
        'donor_id',
        'receiver_id',
        'moderated_by',
        'image_path',
        'picked_up_at',
        'moderated_at',
        'moderation_reason',
    ];

    public function donor()
    {
        return $this->belongsTo(User::class, 'donor_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function moderator()
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    public function isExpired(): bool
    {
        return $this->status === 'available' && $this->expiry_time->isPast();
    }

    public function getDisplayStatusAttribute(): string
    {
        return $this->isExpired() ? 'expired' : $this->status;
    }

    protected $casts = [
        'expiry_time' => 'datetime',
        'picked_up_at' => 'datetime',
        'moderated_at' => 'datetime',
        'is_hidden' => 'boolean',
    ];
}
