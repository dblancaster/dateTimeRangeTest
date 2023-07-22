<?php

namespace Services;

use Data\CalculatedResponseData;
use Data\PublicHoliday;
use DateTime;

class DateTimeService
{

    public ?array $lazyLoadedPublicHolidays;

    public function calculateFromAndToRanges($state, DateTime $from, DateTime $to): CalculatedResponseData
    {
        $calculations = new CalculatedResponseData($state, clone $from, clone $to);
        $this->calculateDaysInRange($calculations);
        return $calculations;
    }

    public function calculateDaysInRange(CalculatedResponseData $calculations)
    {
        $from = clone $calculations->from;
        $to = clone $calculations->to;
        $totalDays = 0;
        $weekendDays = 0;
        $publicHolidayDays = 0;
        while ($from < $to) {
            $totalDays++;
            if ($this->isDateOnAWeekend($from)) {
                $weekendDays++;
            }
            if ($this->isDateOnAPublicHoliday($state, $from)) {
                $publicHolidayDays++;
            }
            $from->modify('+1 day');
        }
        $calculations->totalDays = $totalDays;

        $calculations->daysSkippingWeekends = $totalDays - $weekendDays;
        $calculations->daysSkippingPublicHolidays = $totalDays - $publicHolidayDays;
        $calculations->daysSkippingWeekendAndPublicHolidays = $totalDays - $weekendDays - $publicHolidayDays;

        $diff = $calculations->to->diff($calculations->from);
        $calculations->entireWeeksBetween = floor($diff->days / 7);
        $calculations->totalYears = $diff->y;

        $this->calculateSecondsMinutesHours($calculations);
    }

    public function calculateSecondsMinutesHours(CalculatedResponseData $calculations)
    {
        $calculations->secondsSkippingWeekends = $calculations->daysSkippingWeekends * 86400;
        $calculations->secondsSkippingPublicHolidays = $calculations->daysSkippingPublicHolidays * 86400;
        $calculations->secondsSkippingWeekendAndPublicHolidays = $calculations->daysSkippingWeekendAndPublicHolidays * 86400;
        $calculations->totalSeconds = $calculations->totalDays * 86400;

        $calculations->minutesSkippingWeekends = $calculations->daysSkippingWeekends * 1440;
        $calculations->minutesSkippingPublicHolidays = $calculations->daysSkippingPublicHolidays * 1440;
        $calculations->minutesSkippingWeekendAndPublicHolidays = $calculations->daysSkippingWeekendAndPublicHolidays * 1440;
        $calculations->totalMinutes = $calculations->totalDays * 1440;

        $calculations->hoursSkippingWeekends = $calculations->daysSkippingWeekends * 24;
        $calculations->hoursSkippingPublicHolidays = $calculations->daysSkippingPublicHolidays * 24;
        $calculations->hoursSkippingWeekendAndPublicHolidays = $calculations->daysSkippingWeekendAndPublicHolidays * 24;
        $calculations->totalHours = $calculations->totalDays * 24;
    }

    public function isDateOnAWeekend(DateTime $dateTime): bool
    {
        return in_array($dateTime->format('l'), ["Saturday", "Sunday"]);
    }

    public function isDateOnAPublicHoliday($state, DateTime $dateTime): bool
    {
        $publicHolidaysYMD = $this->getPublicHolidaysAsYMDArrayForState($state);
        return in_array($dateTime->format("Ymd"), $publicHolidaysYMD);
    }

    public function getPublicHolidaysAsYMDArrayForState($state): array
    {
        $publicHolidays = $this->fetchPublicHolidays();
        $publicHolidays = array_filter($publicHolidays, fn($publicHoliday) => $publicHoliday->state == $state);
        return array_map(fn($publicHoliday) => $publicHoliday->dateYMD, $publicHolidays);
    }

    public function fetchPublicHolidaysExternally()
    {
        // public holiday data from http://data.gov.au
        return file_get_contents(__DIR__ . '/example_public_holidays.json');
    }

    /**
     * @return PublicHoliday[]
     */
    public function fetchPublicHolidays(): array
    {
        if (isset($this->lazyLoadedPublicHolidays)) {
            return $this->lazyLoadedPublicHolidays;
        }

        $publicHolidaysArray = json_decode($this->fetchPublicHolidaysExternally(), true);

        $publicHolidays = [];
        foreach ($publicHolidaysArray as $array) {
            $publicHolidayObject = $this->getPublicHolidayFromJSONArray($array);
            if ($publicHolidayObject) {
                $publicHolidays[] = $publicHolidayObject;
            }
        }

        return $this->lazyLoadedPublicHolidays = $publicHolidays;
    }

    public function getPublicHolidayFromJSONArray($array): ?PublicHoliday
    {
        if (!isset($array["Date"])) {
            return null;
        }
        $publicHoliday = new PublicHoliday();
        $publicHoliday->name = $array["Holiday Name"] ?? "";
        $publicHoliday->dateYMD = $array["Date"];
        $publicHoliday->state = $array["Jurisdiction"] ?? "";
        return $publicHoliday;
    }

}
