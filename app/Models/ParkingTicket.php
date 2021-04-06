<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParkingTicket extends Model
{
    use HasFactory;

    const STATUS_ENTERED = 'entered';

    const CATEGORY_A = 'a';
    const CATEGORY_B = 'b';
    const CATEGORY_C = 'c';
    const CATEGORY_SPACES = [
        self::CATEGORY_A => 1,
        self::CATEGORY_B => 2,
        self::CATEGORY_C => 4,
    ];
}
