<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string category
 * @property string entered_at
 * @property string registration_number
 * @property string status
 */
class ParkingTicket extends Model
{
    use HasFactory;

    const STATUS_ENTERED = 'entered';
    const STATUS_EXITED = 'exited';

    const CATEGORY_A = 'a';
    const CATEGORY_B = 'b';
    const CATEGORY_C = 'c';
    const CATEGORY_SPACES = [
        self::CATEGORY_A => 1,
        self::CATEGORY_B => 2,
        self::CATEGORY_C => 4,
    ];

    public function getEnteredAt(): DateTime
    {
        return new DateTime($this->entered_at);
    }

    public function getCategory(): string
    {
        return $this->category;
    }
}
