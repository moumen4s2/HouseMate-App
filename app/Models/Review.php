<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'user_id',
        'apartment_id',
        'booking_id',
        'rating'
    ];

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }
}
