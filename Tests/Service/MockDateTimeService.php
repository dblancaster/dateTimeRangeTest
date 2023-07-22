<?php

namespace Tests;

require_once "Service/DateTimeService.php";

use Data\CalculatedResponseData;
use DateTime;
use Services\DateTimeService;

class MockDateTimeService extends DateTimeService {

    public $overrideCalculateFromAndToRanges;

    public function calculateFromAndToRanges($state, DateTime $from, DateTime $to): CalculatedResponseData
    {
        return $this->overrideCalculateFromAndToRanges ?? parent::calculateFromAndToRanges($state, $from, $to);
    }

}