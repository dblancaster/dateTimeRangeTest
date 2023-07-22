<?php

namespace Services;

use DateTime;
use Controller\DateTimeAPIController;
use DateTimeZone;
use Exception;

/**
 * Ideally would use something like Lumen's validator
 * https://laravel.com/docs/10.x/validation#available-validation-rules
 */
class DateTimeAPIValidatorService
{

    public $state;
    public $from;
    public $to;
    public $fromTimezone;
    public $toTimezone;
    public $errors = [];

    public function validateData($data): void
    {
        $this->validateState($data);
        $this->validateTo($data);
        $this->validateFrom($data);
    }

    public function validateState($data): void
    {
        $this->state = $data["stateForPublicHolidays"] ?? null;
        $allStatesString = implode(", ", DateTimeAPIController::ALL_STATES);
        if (!isset($this->state)) {
            $this->errors[] = "stateForPublicHolidays is required and must be one of $allStatesString";
        } else if (!in_array($this->state, DateTimeAPIController::ALL_STATES)) {
            $this->errors[] = "stateForPublicHolidays must be one of $allStatesString";
        }
    }

    public function validateTo($data): void
    {
        $this->to = $data["to"] ?? null;
        $this->toTimezone = $data["toTimezone"] ?? "Australia/Adelaide";
        $this->to = $this->validateFromOrToDate($this->to, $this->toTimezone, "to");
    }

    public function validateFrom($data): void
    {
        $this->from = $data["from"] ?? null;
        $this->fromTimezone = $data["fromTimezone"] ?? "Australia/Adelaide";
        $this->from = $this->validateFromOrToDate($this->from, $this->fromTimezone, "from");
    }

    public function validateFromOrToDate($date, $timezone, $field) {
        if (!isset($date)) {
            $this->errors[] = "$field is required";
        } else {
            try {
                if (isset($timezone) && !in_array($timezone, DateTimeZone::listIdentifiers())) {
                    $this->errors[] = "$field timezone must in a format similar to Australia/Adelaide";
                } else {
                    $date = new DateTime($date, new DateTimeZone($timezone));
                }
            } catch (Exception $e) {
                $this->errors[] = "Invalid Date Time format for '$field'";
            }
        }
        return $date;
    }

}
