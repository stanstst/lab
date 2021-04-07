<?php

namespace App\Service;

class ParkingHoursDto
{
    /**
     * @var int
     */
    private $dayHours;

    /**
     * @var int
     */
    private $nightHours;

    public function __construct(int $dayHours, int $nightHours)
    {
        $this->dayHours = $dayHours;
        $this->nightHours = $nightHours;
    }

    public function getDayHours(): int
    {
        return $this->dayHours;
    }

    public function getNightHours(): int
    {
        return $this->nightHours;
    }
}
