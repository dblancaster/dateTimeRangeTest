<?php

namespace Services;

use DateTime;
use DateTimeAPIController;
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
    public $action;
    public $responseType;
    public $errors = [];

    public function validateFromRequestData($request): void
    {
        $this->validateState($request);
        $this->validateTo($request);
        $this->validateFrom($request);
        $this->validateAction($request);
        $this->validateResponseType($request);
    }

    public function validateState($request): void
    {
        $this->state = $request["state"] ?? null;
        if (!isset($this->state)) {
            $this->errors[] = "state is required";
        } else if (!in_array($this->state, DateTimeAPIController::ALL_STATES)) {
            $this->errors[] = "state must be one of " . implode(", ", DateTimeAPIController::ALL_STATES);
        }
    }

    public function validateTo($request): void
    {
        $this->to = $request["to"] ?? null;
        if (!isset($this->to)) {
            $this->errors[] = "to is required";
        } else {
            try {
                $this->to = new DateTime($this->to);
            } catch (Exception $e) {
                $this->errors[] = "Invalid Date Time format for 'to'";
            }
        }
    }

    public function validateFrom($request): void
    {
        $this->from = $request["from"] ?? null;
        if (!isset($this->from)) {
            $this->errors[] = "from is required";
        } else {
            try {
                $this->from = new DateTime($this->from);
            } catch (Exception $e) {
                $this->errors[] = "Invalid Date Time format for 'from'";
            }
        }
    }

    public function validateAction($request): void
    {
        $this->action = $request["action"] ?? null;
        if (!isset($this->action)) {
            $this->errors[] = "action is required";
        } else if (!in_array($this->action, DateTimeAPIController::ALL_ACTIONS)) {
            $this->errors[] = "action must be one of " . implode(", ", DateTimeAPIController::ALL_ACTIONS);
        }
    }

    public function validateResponseType($request): void
    {
        $this->responseType = $request["responseType"] ?? null;
        if (isset($this->responseType) && in_array($this->responseType, DateTimeAPIController::RESPONSE_TYPES)) {
            $this->errors[] = "responseType must be one of " . implode(", ", DateTimeAPIController::RESPONSE_TYPES);
        }
    }

}
