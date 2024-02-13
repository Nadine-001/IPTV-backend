<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hotel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'class',
        'check_in',
        'check_out',
        'greeting',
        'about',
        'photo',
        'address',
        'city',
        'phone',
        'longitude',
        'langitude',
        'logo',
        'order_food_intro',
        'qr_code',
    ];

    public function user(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function hotel_facility(): HasMany
    {
        return $this->hasMany(HotelFacilities::class);
    }

    public function menu(): HasMany
    {
        return $this->hasMany(Menu::class);
    }

    public function room(): HasMany
    {
        return $this->hasMany(Room::class);
    }
}
