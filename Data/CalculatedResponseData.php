<?php

namespace Data;

use DateTime;

class CalculatedResponseData
{

    public DateTime $from;
    public DateTime $to;
    public int $totalDays = 0;

    public int $daysSkippingPublicHolidays = 0;
    public int $daysSkippingWeekends = 0;
    public int $daysSkippingWeekendAndPublicHolidays = 0;

    public int $entireWeeksBetween = 0;

    public function __construct(DateTime $from, DateTime $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

}
