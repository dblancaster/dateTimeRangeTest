<?php

namespace Services;

use DateTime;
use Controller\DateTimeAPIController;
use DateTimeZone;
use Exception;

/**
 * If I had time I would ideally use something like Lumen's validator
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

    public function validateFromRequestData($request): void
    {
        $this->validateState($request);
        $this->validateTo($request);
        $this->validateFrom($request);
    }

    public function validateState($request): void
    {
        $this->state = $request["stateForPublicHolidays"] ?? null;
        $allStatesString = implode(", ", DateTimeAPIController::ALL_STATES);
        if (!isset($this->state)) {
            $this->errors[] = "stateForPublicHolidays is required and must be one of $allStatesString";
        } else if (!in_array($this->state, DateTimeAPIController::ALL_STATES)) {
            $this->errors[] = "stateForPublicHolidays must be one of $allStatesString";
        }
    }

    public function validateTo($request): void
    {
        $this->to = $request["to"] ?? null;
        $this->toTimezone = $request["toTimezone"] ?? "Australia/Adelaide";

        if (!isset($this->to)) {
            $this->errors[] = "to is required";
        } else {
            try {
                if (isset($this->toTimezone) && !in_array($this->toTimezone, DateTimeZone::listIdentifiers())) {
                    $this->errors[] = "to timezone must in a format similar to Australia/Adelaide";
                } else {
                    $this->to = new DateTime($this->to, new DateTimeZone($this->toTimezone));
                }
            } catch (Exception $e) {
                $this->errors[] = "Invalid Date Time format for 'to'";
            }
        }
    }

    public function validateFrom($request): void
    {
        $this->from = $request["from"] ?? null;
        $this->fromTimezone = $request["fromTimezone"] ?? "Australia/Adelaide";

        if (!isset($this->from)) {
            $this->errors[] = "from is required";
        } else {
            try {
                if (isset($this->fromTimezone) && !in_array($this->fromTimezone, DateTimeZone::listIdentifiers())) {
                    $this->errors[] = "from timezone must in a format similar from Australia/Adelaide";
                } else {
                    $this->from = new DateTime($this->from, new DateTimeZone($this->fromTimezone));
                }
            } catch (Exception $e) {
                $this->errors[] = "Invalid Date Time format for 'from'";
            }
        }
    }

}
