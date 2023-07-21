<?php

namespace Services;

use Data\CalculatedResponseData;
use Data\PublicHoliday;
use DateTime;

class DateTimeService
{

    public array $lazyLoadedPublicHolidays;

    public function calculateFromAndToRanges($state, DateTime $from, DateTime $to): CalculatedResponseData
    {
        $calculations = new CalculatedResponseData(clone $from, $to);
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

        $calculations->entireWeeksBetween = floor($calculations->to->diff($calculations->from)->days / 7);
        return $calculations;
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

    /**
     * @return PublicHoliday[]
     */
    public function fetchPublicHolidays(): array
    {
        if (isset($this->lazyLoadedPublicHolidays)) {
            return $this->lazyLoadedPublicHolidays;
        }

        // public holiday data from http://data.gov.au
        $file = file_get_contents(__DIR__ . '/example_public_holidays.json');
        $publicHolidaysArray = json_decode($file, true);

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
