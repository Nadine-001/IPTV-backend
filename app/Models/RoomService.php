<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RoomService extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'name',
        'image',
        'is_deleted',
    ];

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function room_service_request_detail(): HasOne
    {
        return $this->hasOne(RoomServiceRequestDetail::class);
    }

    public function temp_cart_room(): HasMany
    {
        return $this->hasMany(TempCartFoodService::class);
    }
}
