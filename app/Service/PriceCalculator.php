<?php

namespace App\Service;

use App\Models\ParkingTicket;
use App\Repositories\ParkingTicketRepository;
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

    /**
     * @var ParkingTicketRepository
     */
    private $parkingTicketRepository;

    public function __construct(
        ParkingHoursCalculator $parkingHoursCalculator,
        ParkingTicketRepository $parkingTicketRepository
    ) {
        $this->parkingHoursCalculator = $parkingHoursCalculator;
        $this->parkingTicketRepository = $parkingTicketRepository;
    }

    /**
     * @param string $registrationNumber
     * @param $dateTo
     * @return float
     */
    public function calculate(string $registrationNumber, DateTime $dateTo): float
    {
        $parkingTicket = $this->parkingTicketRepository->findOneByRegistrationNumber($registrationNumber);
        $parkingHours = $this->parkingHoursCalculator->get($parkingTicket->getEnteredAt(), $dateTo);

        $rate = self::RATES[$parkingTicket->getCategory()];
        $discountMultiplier = ParkingTicket::DISCOUNTS[$parkingTicket->getDiscountCard()];

        return ($parkingHours->getDayHours() * $rate[self::DAY] + $parkingHours->getNightHours() * $rate[self::NIGHT])
            * $discountMultiplier;
    }
}
