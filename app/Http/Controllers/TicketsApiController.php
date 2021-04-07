<?php

namespace App\Http\Controllers;

use App\Models\ParkingTicket;
use App\RequestDto\CheckinDto;
use App\RequestDto\CheckoutDto;
use App\Service\AvailabilityCalculator;
use App\Service\ParkingTicketCheckout;
use App\Service\ParkingTicketCheckin;
use App\Service\PriceCalculator;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class TicketsApiController extends Controller
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ParkingTicketCheckin
     */
    private $parkingTicketCheckin;

    /**
     * @var ParkingTicketCheckout
     */
    private $parkingTicketCheckout;

    /**
     * @var AvailabilityCalculator
     */
    private $availabilityCalculator;

    /**
     * @var PriceCalculator
     */
    private $priceCalculator;

    public function __construct(
        ParkingTicketCheckin $parkingTicketCheckin,
        ParkingTicketCheckout $parkingTicketCheckout,
        AvailabilityCalculator $availabilityCalculator,
        PriceCalculator $priceCalculator,
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->parkingTicketCheckin = $parkingTicketCheckin;
        $this->parkingTicketCheckout = $parkingTicketCheckout;
        $this->availabilityCalculator = $availabilityCalculator;
        $this->priceCalculator = $priceCalculator;
    }

    public function create(Request $request)
    {
        try {
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

            $parkingTicket = $this->parkingTicketCheckin->checkin(new CheckinDto($request));
        } catch (ValidationException $exception) {

            return new JsonResponse(['errors' => $exception->errors(), Response::HTTP_BAD_REQUEST]);
        } catch (Throwable $exception) {
            $this->logger->error($exception->getMessage(), ['exception' => $exception]);

            return new JsonResponse(['Error creating a ticket.'], Response::HTTP_BAD_GATEWAY);
        }

        return new JsonResponse($parkingTicket);
    }

    public function checkout(Request $request)
    {
        try {
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


            $parkingTicket = $this->parkingTicketCheckout->checkout(new CheckoutDto($request));
        } catch (ValidationException $exception) {

            return new JsonResponse(['errors' => $exception->errors(), Response::HTTP_BAD_REQUEST]);
        } catch (Throwable $exception) {
            $this->logger->error($exception->getMessage(), ['exception' => $exception]);

            return new JsonResponse(['Error checking out.'], Response::HTTP_BAD_GATEWAY);
        }

        return new JsonResponse($parkingTicket);
    }

    public function getAvailability(Request $request)
    {
        try {
            $availableSpaces = $this->availabilityCalculator->calculate();
        } catch (Throwable $exception) {
            $this->logger->error($exception->getMessage(), ['exception' => $exception]);

            return new JsonResponse(['Error getting availability.'], Response::HTTP_BAD_GATEWAY);
        }

        return new JsonResponse(['availableSpaces' => $availableSpaces]);
    }

    public function getPrice(Request $request)
    {
        try {
            $availableSpaces = $this->priceCalculator->calculate($request);
        } catch (Throwable $exception) {
            $this->logger->error($exception->getMessage(), ['exception' => $exception]);

            return new JsonResponse(['Error getting availability.'], Response::HTTP_BAD_GATEWAY);
        }

        return new JsonResponse(['availableSpaces' => $availableSpaces]);
    }

}
