<?php

namespace App\Http\Controllers;

use App\Service\AvailabilityCalculator;
use App\Service\ParkingTicketCheckout;
use App\Service\ParkingTicketCheckin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

    public function __construct(
        ParkingTicketCheckin $parkingTicketCheckin,
        ParkingTicketCheckout $parkingTicketCheckout,
        AvailabilityCalculator $availabilityCalculator,
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->parkingTicketCheckin = $parkingTicketCheckin;
        $this->parkingTicketCheckout = $parkingTicketCheckout;
        $this->availabilityCalculator = $availabilityCalculator;
    }

    public function create(Request $request)
    {
        try {
            $parkingTicket = $this->parkingTicketCheckin->checkin($request);
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
            $parkingTicket = $this->parkingTicketCheckout->checkout($request);
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

}
