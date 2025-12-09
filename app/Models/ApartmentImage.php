<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Model;

class ApartmentImage extends Model
{
    use HasFactory;
    protected $fillable = [
        'apartment_id',
        'url',
        'is_main'
    ];
    
    protected $appends = ['full_url'];

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }

    public function getFullUrlAttribute()
    {
        return asset('storage/' . $this->url);
    }

}
