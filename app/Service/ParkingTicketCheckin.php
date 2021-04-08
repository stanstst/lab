<?php

namespace App\Service;

use App\Models\ParkingTicket;
use App\RequestDto\CheckinDto;
use DateTime;

class ParkingTicketCheckin
{
    public function checkin(CheckinDto $requestData): ParkingTicket
    {

        $parkingTicket = new ParkingTicket();
        $parkingTicket->registration_number = $requestData->getRegistrationNumber();
        $parkingTicket->category = $requestData->getCategory();
        $parkingTicket->status = ParkingTicket::STATUS_ENTERED;
        $parkingTicket->discount_card = $requestData->getDiscountCard()? : ParkingTicket::DISCOUNT_CARD_NONE;
        $parkingTicket->entered_at = new DateTime();

        $parkingTicket->save();

        return $parkingTicket;
    }
}
