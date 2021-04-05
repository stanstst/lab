<?php

namespace App\Http\Controllers;

use App\Models\ParkingTicket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Throwable;

class TicketsApiController extends Controller
{
    public function create(Request $request)
    {
        try {
            $parkingTicket = $this->insertTicket($request);
        } catch (ValidationException $exception) {

            return new JsonResponse(['errors' => $exception->errors()]);
        } catch (Throwable $exception) {

            return new JsonResponse('Error creating a ticket.');
        }

        return new JsonResponse([$parkingTicket]);
    }

    private function insertTicket(Request $request): ParkingTicket
    {
        $request->validate(
            [
                'registration_number' => 'required',
                'category' => 'required',
            ]
        );

        $parkingTicket = new ParkingTicket();
        $parkingTicket->registration_number = $request->registration_number;
        $parkingTicket->category = $request->category;
        $parkingTicket->status = ParkingTicket::STATUS_ENTERED;
        $parkingTicket->entered_at = new \DateTime();

        $parkingTicket->save();

        return $parkingTicket;
    }
}
