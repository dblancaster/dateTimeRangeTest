<?php

namespace Data;

use DateTime;

class CalculatedResponseData
{
    public DateTime $originalFrom;
    public DateTime $originalTo;
    public int $totalDays = 0;

    public int $daysWithoutPublicHoliday = 0;
    public int $daysWithoutWeekend = 0;
    public int $daysWithoutWeekendAndPublicHolidays = 0;

    public int $entireWeeksBetween = 0;

    public int $businessTimeInSeconds = 0;
    public int $businessTimeInMinutes = 0;
    public int $businessTimeInHours = 0;

    public function __construct(DateTime $from, DateTime $to)
    {
        $this->originalFrom = $from;
        $this->originalTo = $to;
    }


}