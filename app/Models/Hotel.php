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
        'qr_code_payment',
        'qr_code_wifi',
    ];

    public function user(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function hotel_facility(): HasMany
    {
        return $this->hasMany(HotelFacilities::class);
    }

    public function room(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function room_service(): HasMany
    {
        return $this->hasMany(RoomService::class);
    }

    public function television(): HasMany
    {
        return $this->hasMany(Television::class);
    }

    public function menu(): HasMany
    {
        return $this->hasMany(Menu::class);
    }

    public function room_service_request(): HasMany
    {
        return $this->hasMany(RoomServiceRequest::class);
    }

    public function food_service_request(): HasMany
    {
        return $this->hasMany(FoodServiceRequest::class);
    }

    public function temp_cart_room(): HasMany
    {
        return $this->hasMany(TempCartFoodService::class);
    }

    public function temp_cart_food(): HasMany
    {
        return $this->hasMany(TempCartFoodService::class);
    }
}
