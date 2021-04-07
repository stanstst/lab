<?php

namespace App\Service;

use DateTime;

class ParkingHoursCalculator
{
    public function get(DateTime $from, DateTime $to): ParkingHoursDto
    {
        return new ParkingHoursDto(1, 1);
    }
}
