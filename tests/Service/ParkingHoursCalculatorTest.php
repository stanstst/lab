<?php

namespace Tests\Service;

use App\Service\ParkingHoursCalculator;
use App\Service\ParkingHoursDto;
use DateTime;
use PHPUnit\Framework\TestCase;

class ParkingHoursCalculatorTest extends TestCase
{

    /**
     * @var ParkingHoursCalculator
     **/
    protected $calculator;

    public function setUp(): void
    {
        parent::setUp();

        $this->calculator = new ParkingHoursCalculator();
    }

    /**
     * @dataProvider getTimes
     */
    public function testCalculate(DateTime $from, DateTime $to, ParkingHoursDto $expectedResult): void
    {
        $actual = $this->calculator->get($from, $to);
        $this->assertEquals($expectedResult, $actual);
    }

    public function getTimes()
    {
        return [
            // test init
            [new DateTime('2021-01-01 14:00:00'), new DateTime('2021-01-01 14:59:00'), new ParkingHoursDto(1, 0)],
            [new DateTime('2021-01-01 18:00:00'), new DateTime('2021-01-01 18:01:00'), new ParkingHoursDto(0, 1)],
            [new DateTime('2021-01-02 00:00:00'), new DateTime('2021-01-02 00:01:00'), new ParkingHoursDto(0, 1)],
            [new DateTime('2021-01-02 07:55:00'), new DateTime('2021-01-02 08:05:00'), new ParkingHoursDto(0, 1)],

            // start in day
            [new DateTime('2021-01-02 14:00:00'), new DateTime('2021-01-02 15:00:00'), new ParkingHoursDto(2, 0)],
            [new DateTime('2021-01-02 14:00:00'), new DateTime('2021-01-02 18:00:00'), new ParkingHoursDto(4, 1)],
            [new DateTime('2021-01-02 14:00:00'), new DateTime('2021-01-03 12:59:59'), new ParkingHoursDto(9, 14)],

            // start in night
            [new DateTime('2021-01-02 07:00:00'), new DateTime('2021-01-02 08:59:59'), new ParkingHoursDto(1, 1)],
            [new DateTime('2021-01-02 07:00:00'), new DateTime('2021-01-02 09:59:59'), new ParkingHoursDto(2, 1)],
            [new DateTime('2021-01-02 07:00:00'), new DateTime('2021-01-02 18:59:59'), new ParkingHoursDto(10, 2)],
            [new DateTime('2021-01-02 06:59:59'), new DateTime('2021-01-02 18:59:59'), new ParkingHoursDto(10, 3)],
        ];
    }
}
