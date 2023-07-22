<?php

namespace Tests;

use Controller\DateTimeAPIController;
use Data\CalculatedResponseData;
use Data\PublicHoliday;
use DateTime;
use Services\DateTimeService;

class DateTimeServiceTest extends DateTimeTestBase
{

    public MockDateTimeService $dateTimeService;

    public function __construct()
    {
        $this->dateTimeService = new MockDateTimeService();
    }

    public function runTests()
    {
        $this->testCalculateSecondsMinutesHours();
        $this->testIsDateOnAWeekend();
        $this->testIsDateOnAPublicHoliday();
        $this->testFetchPublicHolidays();
        $this->testGetPublicHolidaysAsYMDArrayForState();
        $this->testCalculateDaysInRange();
        $this->outputResults();
    }

    public function testCalculateDaysInRange()
    {
        $calculations = new CalculatedResponseData(DateTimeAPIController::STATE_SA, new DateTime("2021-03-04"), new DateTime("2021-05-14"));
        $this->dateTimeService->calculateDaysInRange($calculations);

        $expected = '{
  "from": {
    "date": "2021-03-04 00:00:00.000000",
    "timezone_type": 3,
    "timezone": "Australia/Adelaide"
  },
  "to": {
    "date": "2021-05-14 00:00:00.000000",
    "timezone_type": 3,
    "timezone": "Australia/Adelaide"
  },
  "totalSeconds": 6134400,
  "totalMinutes": 102240,
  "totalHours": 1704,
  "totalDays": 71,
  "totalYears": 0,
  "daysSkippingPublicHolidays": 71,
  "daysSkippingWeekends": 51,
  "daysSkippingWeekendAndPublicHolidays": 51,
  "secondsSkippingPublicHolidays": 6134400,
  "secondsSkippingWeekends": 4406400,
  "secondsSkippingWeekendAndPublicHolidays": 4406400,
  "minutesSkippingPublicHolidays": 102240,
  "minutesSkippingWeekends": 73440,
  "minutesSkippingWeekendAndPublicHolidays": 73440,
  "hoursSkippingPublicHolidays": 1704,
  "hoursSkippingWeekends": 1224,
  "hoursSkippingWeekendAndPublicHolidays": 1224,
  "entireWeeksBetween": 10,
  "state": "sa"
}';
        $this->assertSame(json_decode($expected, true), json_decode(json_encode($calculations), true));
    }

    public function testCalculateSecondsMinutesHours()
    {
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

    public function testIsDateOnAWeekend()
    {
        $data = [
            "2023-07-22" => true,
            "2023-07-23" => true,
            "2023-07-24" => false,
            "2023-07-25" => false,
            "2023-07-26" => false,
            "2023-07-27" => false,
            "2023-07-28" => false,
            "2023-07-29" => true,
        ];
        foreach ($data as $dateString => $expected) {
            $actual = $this->dateTimeService->isDateOnAWeekend(new DateTime($dateString));
            $this->assertSame($expected, $actual);
        }
    }

    public function testIsDateOnAPublicHoliday()
    {
        $data = [
            "2023-01-26" => true, // Australia Day
            "2023-01-25" => false
        ];
        foreach ($data as $dateString => $expected) {
            $actual = $this->dateTimeService->isDateOnAPublicHoliday("sa", new DateTime($dateString));
            $this->assertSame($expected, $actual);
        }
    }

    public function testFetchPublicHolidays()
    {
        $service = $this->dateTimeService;
        $service->lazyLoadedPublicHolidays = ["abc"];
        $this->assertSame(["abc"], $service->fetchPublicHolidays());

        $service->lazyLoadedPublicHolidays = null;
        $holidays = $service->fetchPublicHolidays();
        $this->assertSame(1, count($holidays));
        $holiday = $holidays[0];
        $this->assertSame(true, $holiday instanceof PublicHoliday);
        $this->assertSame("sa", $holiday->state);
        $this->assertSame("Australia Day", $holiday->name);
        $this->assertSame("20230126", $holiday->dateYMD);
        $this->assertSame($holidays, $service->lazyLoadedPublicHolidays);
    }

    public function testGetPublicHolidaysAsYMDArrayForState()
    {
        $service = $this->dateTimeService;
        $holidays = $service->getPublicHolidaysAsYMDArrayForState("sa");
        $this->assertSame(1, count($holidays));

        $holidays = $service->getPublicHolidaysAsYMDArrayForState("nsw");
        $this->assertSame(0, count($holidays));
    }

}
