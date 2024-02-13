<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomService extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'name',
        'image',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
