<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuestHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'room_number',
        'guest_name',
        'check_in',
        'check_out',
    ];
}
