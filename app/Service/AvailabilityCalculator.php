<?php

namespace App\Service;

use App\Models\ParkingTicket;

class AvailabilityCalculator
{
    // @todo extract this in config
    private const TOTAL_SPACES= 200;

    public function calculate(): int
    {
        /** @var array<ParkingTicket> $openedTickets */
        $openedTickets = ParkingTicket::where(['status' => ParkingTicket::STATUS_ENTERED])->get();
        $takenSpaces = 0;

        foreach ($openedTickets as $ticket) {
            $takenSpaces += ParkingTicket::CATEGORY_SPACES[$ticket->category];
        }

        return self::TOTAL_SPACES - $takenSpaces;
    }

}
