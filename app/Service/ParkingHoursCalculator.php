<?php

namespace App\Service;

use DateInterval;
use DateTime;

class ParkingHoursCalculator
{
    const DAY_START = 8;
    const NIGHT_START = 18;
    const ONE_HOUR_INTERVAL = 'PT1H';

    public function get(DateTime $from, DateTime $to): ParkingHoursDto
    {
        list($day, $night) = $this->initStart($from);

        $hourCounter = clone $from;
        do {
            $hourCounter->add(new DateInterval(self::ONE_HOUR_INTERVAL));

            if ($hourCounter <= $to) {
                if ($this->isDay($hourCounter)) {
                    $day++;
                } else {
                    $night++;
                }
            }


        } while($hourCounter < $to);

        return new ParkingHoursDto($day, $night);
    }

    private function getHour(DateTime $from): int
    {
        return (int)$from->format('H');
    }

    private function initStart(DateTime $from): array
    {
        $day = 0;
        $night = 1;

        if ($this->isDay($from)) {
            $day = 1;
            $night = 0;
        }

        return array($day, $night);
    }

    private function isDay(DateTime $dateTime): bool
    {
        return $this->getHour($dateTime) >= self::DAY_START && $this->getHour($dateTime) < self::NIGHT_START;
    }
}
