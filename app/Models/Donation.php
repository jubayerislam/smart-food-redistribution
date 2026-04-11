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
        'donor_id',
        'receiver_id',
        'image_path',
        'picked_up_at',
    ];

    public function donor()
    {
        return $this->belongsTo(User::class, 'donor_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    protected $casts = [
        'expiry_time' => 'datetime',
        'picked_up_at' => 'datetime',
    ];
}
