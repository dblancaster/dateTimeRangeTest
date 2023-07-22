<?php

namespace Tests;

use Data\CalculatedResponseData;
use DateTime;
use Services\DateTimeService;

class MockDateTimeService extends DateTimeService
{

    public $overrideCalculateFromAndToRanges;

    public function calculateFromAndToRanges($state, DateTime $from, DateTime $to): CalculatedResponseData
    {
        return $this->overrideCalculateFromAndToRanges ?? parent::calculateFromAndToRanges($state, $from, $to);
    }

    public function fetchPublicHolidaysExternally()
    {
        return '[{
    "_id": 281,
    "ID": "281",
    "Date": "20230126",
    "Holiday Name": "Australia Day",
    "Information": "Always celebrated on 26 January",
    "More Information": "https://www.safework.sa.gov.au/resources/public-holidays",
    "Jurisdiction": "sa"
  }]';
    }

}