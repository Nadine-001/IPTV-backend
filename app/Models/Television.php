<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Television extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'mac_address',
        'guest_name',
        'guest_gender',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
