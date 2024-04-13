<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'type',
        'name',
        'description',
        'price',
        'image',
        'is_deleted',
    ];

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function food_service_request_detail(): HasOne
    {
        return $this->hasOne(FoodServiceRequestDetail::class);
    }

    public function temp_cart_food(): HasMany
    {
        return $this->hasMany(TempCartFoodService::class);
    }
}
