<?php

namespace App\Service;

use App\Models\ParkingTicket;
use DateTime;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ParkingTicketCheckin
{
    /**
     * @throws ValidationException
     */
    public function checkin(Request $request): ParkingTicket
    {
        $request->validate(
            [
                'registration_number' => [
                    'required',
                    'alpha_num',
                    Rule::unique('parking_tickets')->where(
                        function (Builder $query) use ($request) {
                            return $query->where('registration_number', $request->registration_number)
                                ->where('status', ParkingTicket::STATUS_ENTERED);
                        }
                    ),
                ],
                'category' => [
                    'required',
                    Rule::in(array_keys(ParkingTicket::CATEGORY_SPACES)),
                ],
            ]
        );

        $parkingTicket = new ParkingTicket();
        $parkingTicket->registration_number = $request->registration_number;
        $parkingTicket->category = $request->category;
        $parkingTicket->status = ParkingTicket::STATUS_ENTERED;
        $parkingTicket->entered_at = new DateTime();

        $parkingTicket->save();

        return $parkingTicket;
    }
}
