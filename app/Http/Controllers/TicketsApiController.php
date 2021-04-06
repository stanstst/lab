<?php

namespace App\Http\Controllers;

use App\Models\ParkingTicket;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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

    /**
     * @throws ValidationException
     */
    private function insertTicket(Request $request): ParkingTicket
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
        $parkingTicket->entered_at = new \DateTime();

        $parkingTicket->save();

        return $parkingTicket;
    }
}
