<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TempCartFoodService extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'television_id',
        'menu_id',
        'qty',
    ];

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function television(): BelongsTo
    {
        return $this->belongsTo(Television::class);

    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);

    }
}
