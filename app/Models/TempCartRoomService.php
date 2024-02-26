<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TempCartRoomService extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'television_id',
        'room_service_id',
        'qty',
        'note',
    ];

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function television(): BelongsTo
    {
        return $this->belongsTo(Television::class);

    }

    public function room_service(): BelongsTo
    {
        return $this->belongsTo(RoomService::class);

    }
}
