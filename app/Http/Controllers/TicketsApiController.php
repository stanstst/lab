<?php

namespace App\Http\Controllers;

use App\Models\ParkingTicket;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class TicketsApiController extends Controller
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function create(Request $request)
    {
        try {
            $parkingTicket = $this->insertTicket($request);
        } catch (ValidationException $exception) {

            return new JsonResponse(['errors' => $exception->errors(), Response::HTTP_BAD_REQUEST]);
        } catch (Throwable $exception) {
            $this->logger->error($exception->getMessage(), ['exception' => $exception]);

            return new JsonResponse(['Error creating a ticket.'], Response::HTTP_BAD_GATEWAY);
        }

        return new JsonResponse($parkingTicket);
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

    public function checkout(Request $request)
    {
        try {
            $parkingTicket = $this->checkoutTicket($request);
        } catch (ValidationException $exception) {

            return new JsonResponse(['errors' => $exception->errors(), Response::HTTP_BAD_REQUEST]);
        } catch (Throwable $exception) {
            $this->logger->error($exception->getMessage(), ['exception' => $exception]);

            return new JsonResponse(['Error checking out.'], Response::HTTP_BAD_GATEWAY);
        }

        return new JsonResponse($parkingTicket);
    }
    /**
     * @throws ValidationException
     */

    private function checkoutTicket(Request $request)
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
