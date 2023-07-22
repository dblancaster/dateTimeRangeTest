<?php

namespace Tests;

use Controller\DateTimeAPIController;
use Data\CalculatedResponseData;
use DateTime;
use Services\DateTimeService;

class DateTimeServiceTest extends DateTimeTestBase
{

    public DateTimeService $dateTimeService;

    public function __construct()
    {
        $this->dateTimeService = new DateTimeService();
    }

    public function getTestData()
    {
        return [
            ['from' => strtotime("2021-03-04 02:00:00"), 'to' => strtotime("2021-03-04 20:00:00"), 'expected' => 10.0],
            ['from' => strtotime("2021-03-04 10:00:00"), 'to' => strtotime("2021-03-04 11:30:00"), 'expected' => 1.5],
            ['from' => strtotime("2021-03-04 08:00:00"), 'to' => strtotime("2021-03-04 08:30:00"), 'expected' => 0.5],
            ['from' => strtotime("2021-03-04 07:00:00"), 'to' => strtotime("2021-03-04 08:00:00"), 'expected' => 0],
            ['from' => strtotime("2021-03-04 17:30:00"), 'to' => strtotime("2021-03-04 18:00:00"), 'expected' => 0.5],
            ['from' => strtotime("2021-03-04 18:00:00"), 'to' => strtotime("2021-03-04 18:30:00"), 'expected' => 0],
            ['from' => strtotime("2021-03-04 18:00:00"), 'to' => strtotime("2021-03-05 08:00:00"), 'expected' => 0],
            ['from' => strtotime("2021-03-04 18:00:00"), 'to' => strtotime("2021-03-05 09:00:00"), 'expected' => 1.0],
            ['from' => strtotime("2021-03-04 18:00:00"), 'to' => strtotime("2021-03-10 08:00:00"), 'expected' => 30.0]
        ];
    }

    public function runTests()
    {
        $this->testCalculateSecondsMinutesHours();
        $this->outputResults();
    }

    public function testCalculateSecondsMinutesHours() {
        $calculations = new CalculatedResponseData(DateTimeAPIController::STATE_SA, new DateTime(), new DateTime());
        $calculations->daysSkippingWeekends = 11;
        $calculations->daysSkippingPublicHolidays = 13;
        $calculations->daysSkippingWeekendAndPublicHolidays = 15.5;
        $this->dateTimeService->calculateSecondsMinutesHours($calculations);
        $this->assertSame(312, $calculations->hoursSkippingPublicHolidays);
        $this->assertSame(264, $calculations->hoursSkippingWeekends);
        $this->assertSame(360, $calculations->hoursSkippingWeekendAndPublicHolidays);
        $this->assertSame(18720, $calculations->minutesSkippingPublicHolidays);
        $this->assertSame(15840, $calculations->minutesSkippingWeekends);
        $this->assertSame(21600, $calculations->minutesSkippingWeekendAndPublicHolidays);
        $this->assertSame(1123200, $calculations->secondsSkippingPublicHolidays);
        $this->assertSame(950400, $calculations->secondsSkippingWeekends);
        $this->assertSame(1296000, $calculations->secondsSkippingWeekendAndPublicHolidays);
    }

}
