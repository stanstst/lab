<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string category
 * @property string entered_at
 * @property string registration_number
 * @property string discount_card
 * @property string status
 */
class ParkingTicket extends Model
{
    use HasFactory;

    const STATUS_ENTERED = 'entered';
    const STATUS_EXITED = 'exited';

    // @todo extract these as param config
    const CATEGORY_A = 'a';
    const CATEGORY_B = 'b';
    const CATEGORY_C = 'c';
    const CATEGORY_SPACES = [
        self::CATEGORY_A => 1,
        self::CATEGORY_B => 2,
        self::CATEGORY_C => 4,
    ];

    const DISCOUNT_CARD_NONE = 'card-none';
    const DISCOUNT_CARD_SILVER = 'card-silver';
    const DISCOUNT_CARD_GOLD = 'card-gold';
    const DISCOUNT_CARD_PLATINUM = 'card-platinum';

    // @todo extract these as param config
    const DISCOUNTS = [
        self::DISCOUNT_CARD_NONE => 1,
        self::DISCOUNT_CARD_SILVER => 0.9,
        self::DISCOUNT_CARD_GOLD => 0.85,
        self::DISCOUNT_CARD_PLATINUM => 0.80,
    ];

    public function getEnteredAt(): DateTime
    {
        return new DateTime($this->entered_at);
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getDiscountCard(): string
    {
        return $this->discount_card;
    }
}
