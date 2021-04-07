<?php

namespace App\Service;

use App\Models\ParkingTicket;
use App\RequestDto\CheckoutDto;
use InvalidArgumentException;

class ParkingTicketCheckout
{

    public function checkout(CheckoutDto $request)
    {
        $count = ParkingTicket::where('registration_number', $request->getRegistrationNumber())
            ->where('status', ParkingTicket::STATUS_ENTERED)
            ->count();

        if ($count !== 1) {
            throw new InvalidArgumentException(sprintf('Multiple or none registration entry %s', $request->getRegistrationNumber()));
        }

        $parkingTicket = ParkingTicket::where('registration_number', $request->getRegistrationNumber())
            ->where('status', ParkingTicket::STATUS_ENTERED)
            ->first();

        $parkingTicket->status = ParkingTicket::STATUS_EXITED;
        $parkingTicket->save();

        return $parkingTicket;

    }

}
