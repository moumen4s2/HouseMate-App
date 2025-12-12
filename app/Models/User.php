<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'role',
        'first_name',
        'last_name',
        'phone',
        'password',
        'avatar_url',
        'id_document_url',
        'is_approved',
        'email_verified_at',
        'email',
        'otp',
        'expire_at',
        'date_of_birth',
        'mode',
        'dir'
    ];

    protected $hidden = [
        'password',
        'otp',
        'expire_at'
    ];

    protected function casts(): array
    {
        return [
            'password'=>'hashed',
            'expire_at' => 'datetime',
        ];
    }
    // Relationships
    public function apartments()
    {
        return $this->hasMany(Apartment::class, 'owner_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'tenant_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'tenant_id');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function messagesSent()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function messagesReceived()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
