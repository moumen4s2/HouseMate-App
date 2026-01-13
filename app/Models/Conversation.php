<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = [
        'tenant_id',
        'owner_id',
        'apartment_id'
    ];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }
}
