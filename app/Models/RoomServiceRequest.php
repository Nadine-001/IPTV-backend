<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RoomServiceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'television_id',
        'is_accepted',
    ];

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function television(): BelongsTo
    {
        return $this->belongsTo(Television::class);

    }

    public function room_service_request_detail(): HasMany
    {
        return $this->hasMany(RoomServiceRequestDetail::class);
    }
}
