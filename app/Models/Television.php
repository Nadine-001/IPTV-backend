<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }
}
