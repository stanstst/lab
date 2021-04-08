<?php

namespace Tests\Unit\Service;

use App\Models\ParkingTicket;
use App\Repositories\ParkingTicketRepository;
use App\Service\ParkingHoursCalculator;
use App\Service\ParkingHoursDto;
use App\Service\PriceCalculator;
use DateTime;
use PHPUnit\Framework\TestCase;

class PriceCalculatorTest extends TestCase
{
    const REGISTRATION_NUMBER = 'CA3454';

    /**
     * @var PriceCalculator
     **/
    protected $priceCalculator;

    /**
     * @var ParkingHoursCalculator|\Prophecy\Prophecy\ObjectProphecy
     */
    private $parkingHoursCalculator;

    /**
     * @var ParkingTicketRepository|\Prophecy\Prophecy\ObjectProphecy
     */
    private $parkingTicketRepo;

    /**
     * @var ParkingTicket|\Prophecy\Prophecy\ObjectProphecy
     */
    private $ticket;

    public function setUp(): void
    {
        parent::setUp();

        $this->parkingHoursCalculator = $this->prophesize(ParkingHoursCalculator::class);
        $this->parkingTicketRepo = $this->prophesize(ParkingTicketRepository::class);
        $this->priceCalculator = new PriceCalculator(
            $this->parkingHoursCalculator->reveal(),
            $this->parkingTicketRepo->reveal()
        );

        $this->ticket = $this->prophesize(ParkingTicket::class);
    }

    public function testCalculate()
    {
        $this->parkingTicketRepo->findOneByRegistrationNumber(self::REGISTRATION_NUMBER)
            ->willReturn($this->ticket->reveal());

        $dateTimeFrom = new DateTime('2020-02-15 00:00:00');
        $dateTimeTo = new DateTime('2020-02-16 00:00:00');
        $this->ticket->getEnteredAt()->willReturn($dateTimeFrom);
        $this->ticket->getCategory()->willReturn(ParkingTicket::CATEGORY_A);
        $this->ticket->getDiscountCard()->willReturn(ParkingTicket::DISCOUNT_CARD_GOLD);

        $this->parkingHoursCalculator->get($dateTimeFrom, $dateTimeTo)->willReturn(new ParkingHoursDto(2, 3));

        $price = $this->priceCalculator->calculate(self::REGISTRATION_NUMBER, $dateTimeTo);
        $this->assertEquals(10.2, $price);
    }
}
