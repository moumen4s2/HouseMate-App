<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'role',
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'avatar_url',
        'id_document_url',
        'is_approved'
    ];

    protected $hidden = [
        'password',
    ];

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

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }
}
