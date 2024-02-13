<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'number',
        'type',
        'facility',
        'description',
        'image',
        'television',
    ];

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function room_service(): HasMany
    {
        return $this->hasMany(RoomService::class);
    }

    public function television(): HasMany
    {
        return $this->hasMany(Television::class);
    }
}
