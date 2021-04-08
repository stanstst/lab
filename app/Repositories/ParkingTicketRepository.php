<?php

namespace App\Repositories;

use App\Models\ParkingTicket;

class ParkingTicketRepository
{
    public function findOneByRegistrationNumber(string $registrationNumber): ParkingTicket
    {
        /** @var ParkingTicket $parkingTicket */
        $parkingTicket = ParkingTicket::where('registration_number', $registrationNumber)
            ->where('status', ParkingTicket::STATUS_ENTERED)
            ->first();

        return $parkingTicket;
    }

}
