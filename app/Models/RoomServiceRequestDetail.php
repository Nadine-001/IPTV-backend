<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomServiceRequestDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_service_request_id',
        'room_service_id',
        'qty',
    ];

    public function room_service(): BelongsTo
    {
        return $this->belongsTo(RoomService::class);
    }

    public function room_service_request(): BelongsTo
    {
        return $this->belongsTo(RoomServiceRequest::class);
    }
}
