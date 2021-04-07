<?php

namespace App\Service;

use App\Models\ParkingTicket;
use DateTime;

class PriceCalculator
{
    // @todo extract these as param config
    private const DAY = 'day';
    private const NIGHT = 'night';
    private const RATES = [
        ParkingTicket::CATEGORY_A => [self::DAY => 3.00, self::NIGHT => 2.00],
        ParkingTicket::CATEGORY_B => [self::DAY => 6.00, self::NIGHT => 4.00],
        ParkingTicket::CATEGORY_C => [self::DAY => 12.00, self::NIGHT => 8.00],
    ];

    /**
     * @var ParkingHoursCalculator
     */
    private $parkingHoursCalculator;

    public function __construct(ParkingHoursCalculator $parkingHoursCalculator)
    {
        $this->parkingHoursCalculator = $parkingHoursCalculator;
    }

    /**
     * @todo Add discount cart
     */
    public function calculate(string $registrationNumber): float
    {
        /** @var ParkingTicket $parkingTicket */
        $parkingTicket = ParkingTicket::where('registration_number', $registrationNumber)
            ->where('status', ParkingTicket::STATUS_ENTERED)
            ->first();

        $parkingHours = $this->parkingHoursCalculator->get($parkingTicket->getEnteredAt(), new DateTime());

        $rate = self::RATES[$parkingTicket->category];

        return $parkingHours->getDayHours() * $rate[self::DAY] + $parkingHours->getNightHours() * $rate[self::NIGHT];
    }
}
