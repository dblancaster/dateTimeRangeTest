<?php

namespace Data;

use DateTime;

class CalculatedResponseData
{

    public DateTime $from;
    public DateTime $to;

    public int $totalSeconds = 0;
    public int $totalMinutes = 0;
    public int $totalHours = 0;
    public int $totalDays = 0;
    public int $totalYears = 0;

    public int $daysSkippingPublicHolidays = 0;
    public int $daysSkippingWeekends = 0;
    public int $daysSkippingWeekendAndPublicHolidays = 0;

    public int $secondsSkippingPublicHolidays = 0;
    public int $secondsSkippingWeekends = 0;
    public int $secondsSkippingWeekendAndPublicHolidays = 0;

    public int $minutesSkippingPublicHolidays = 0;
    public int $minutesSkippingWeekends = 0;
    public int $minutesSkippingWeekendAndPublicHolidays = 0;

    public int $hoursSkippingPublicHolidays = 0;
    public int $hoursSkippingWeekends = 0;
    public int $hoursSkippingWeekendAndPublicHolidays = 0;

    public int $entireWeeksBetween = 0;

    public function __construct($state, DateTime $from, DateTime $to)
    {
        $this->state = $state;
        $this->from = $from;
        $this->to = $to;
    }

}
