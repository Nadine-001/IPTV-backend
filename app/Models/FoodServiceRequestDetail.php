<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FoodServiceRequestDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'food_service_request_id',
        'menu_id',
        'qty',
    ];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function food_service_request(): BelongsTo
    {
        return $this->belongsTo(FoodServiceRequest::class);
    }
}
