<?php

namespace App\Service;

use App\Models\ParkingTicket;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class ParkingTicketCheckout
{

    /**
     * @throws ValidationException
     */
    public function checkout(Request $request)
    {
        $request->validate(
            [
                'registration_number' => [
                    'required',
                    'alpha_num',
                    Rule::exists('parking_tickets')->where(
                        function (Builder $query) use ($request) {
                            return $query->where('registration_number', $request->registration_number)
                                ->where('status', ParkingTicket::STATUS_ENTERED);
                        }
                    ),
                ],
            ]
        );

        $count = ParkingTicket::where('registration_number', $request->registration_number)
            ->where('status', ParkingTicket::STATUS_ENTERED)
            ->count();

        if ($count !== 1) {
            throw new InvalidArgumentException(sprintf('Multiple or none registration entry %s', $request->registration_number));
        }

        $parkingTicket = ParkingTicket::where('registration_number', $request->registration_number)
            ->where('status', ParkingTicket::STATUS_ENTERED)
            ->first();

        $parkingTicket->status = ParkingTicket::STATUS_EXITED;
        $parkingTicket->save();

        return $parkingTicket;

    }

}
