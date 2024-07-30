<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FoodServiceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'television_id',
        'is_accepted',
        'total',
        'payment_method',
        'qr_code',
        'qr_code_expire_time',
        'is_paid',
        'order_id',
        'is_notified',
        'is_withdrawn',
    ];

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function television(): BelongsTo
    {
        return $this->belongsTo(Television::class);
    }

    public function food_service_request_detail(): HasMany
    {
        return $this->hasMany(FoodServiceRequestDetail::class);
    }
}
