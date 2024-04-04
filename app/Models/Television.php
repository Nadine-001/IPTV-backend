<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Television extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'room_number',
        'room_type',
        'mac_address',
        'guest_name',
        'guest_gender',
        'is_active',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
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
