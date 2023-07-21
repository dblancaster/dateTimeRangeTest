<?php

namespace Services;

use Data\CalculatedResponseData;
use Data\PublicHoliday;
use DateTime;

class DateTimeService
{

    public array $lazyLoadedPublicHolidays;

    public function calculateWorkingHours(CalculatedResponseData $calculations): void
    {
        $to = $calculations->originalTo;
        $from = $calculations->originalFrom;

        $workdayStartHour = 8;
        $workdayEndHour = 18;

        $numberOfWorkingDays = max(0, $calculations->daysWithoutWeekendAndPublicHolidays - 1);

        // 8am to 6pm
        $fromHours = min($workdayEndHour, max($workdayStartHour, $from->format("H")));
        $fromMinutes = $from->format("i");
        if ($fromHours >= 18) {
            $fromMinutes = 0;
        }

        $toHours = min($workdayEndHour, max($workdayStartHour, $to->format("H")));
        $toMinutes = $to->format("i");
        if ($toHours >= 18) {
            $toMinutes = 0;
        }

        $startTimeInSeconds = $fromHours * 3600 + $fromMinutes * 60;
        $endTimeInSeconds = $toHours * 3600 + $toMinutes * 60;

        $numberOfSecondsInWorkDay = ($workdayEndHour - $workdayStartHour) * 3600;

        // calculate number of hours difference, 10 working hours per day
        $seconds = ($numberOfWorkingDays * $numberOfSecondsInWorkDay) + $endTimeInSeconds - $startTimeInSeconds;
        $calculations->businessTimeInSeconds = $seconds;
        $calculations->businessTimeInMinutes = $seconds * 60;
        $calculations->businessTimeInHours = $seconds * 3600;
    }

    public function calculateFromAndToRanges($state, DateTime $from, DateTime $to): CalculatedResponseData
    {
        $calculations = new CalculatedResponseData($from, $to);
        while ($from < $to) {
            $calculations->totalDays++;
            $isWeekend = $this->isDateOnAWeekend($from);
            if (!$isWeekend) {
                $calculations->daysWithoutWeekend++;
            }
            if ($state) {
                $isPublicHoliday = $this->isDateOnAPublicHoliday($state, $from);
                if (!$isPublicHoliday) {
                    $calculations->daysWithoutPublicHoliday++;
                }
                if (!$isWeekend && !$isPublicHoliday) {
                    $calculations->daysWithoutWeekendAndPublicHolidays++;
                }
            }
            $from->modify('+1 day');
        }

        $calculations->entireWeeksBetween = floor($to->diff($from)->days / 7);
        return $calculations;
    }

    public function isDateOnAWeekend(DateTime $dateTime): bool
    {
        return in_array($dateTime->format('l'), ["Saturday", "Sunday"]);
    }

    public function isDateOnAPublicHoliday($state, DateTime $dateTime): bool
    {
        return in_array($dateTime->format("Y-m-d"), $this->getPublicHolidaysAsYMDArrayForState($state));
    }

    public function getPublicHolidaysAsYMDArrayForState($state): array
    {
        return array_map(fn($publicHoliday) => $publicHoliday->dateYMD && $publicHoliday->state == $state, $this->fetchPublicHolidays());
    }

    public function fetchPublicHolidays(): array
    {
        if ($this->lazyLoadedPublicHolidays) {
            return $this->lazyLoadedPublicHolidays;
        }

        // public holiday data from http://data.gov.au
        $publicHolidaysArray = json_decode(file_get_contents('example_public_holidays.json'), true);

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
        if (isset($array["Date"])) {
            return null;
        }
        $publicHoliday = new PublicHoliday();
        $publicHoliday->name = $array["Holiday Name"] ?? "";
        $publicHoliday->dateYMD = $array["Date"];
        $publicHoliday->state = $array["Jurisdiction"] ?? "";
        return $publicHoliday;
    }

}
