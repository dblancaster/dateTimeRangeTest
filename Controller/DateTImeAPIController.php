<?php

namespace Controller;

use Services\DateTimeAPIValidatorService;
use Services\DateTimeService;

class DateTimeAPIController
{

    const STATE_SA = 'sa';
    const STATE_NSW = 'nsw';
    const STATE_VIC = 'vic';

    const ALL_STATES = [
        self::STATE_SA,
        self::STATE_NSW,
        self::STATE_VIC
    ];

    public DateTimeService $service;

    public function __construct()
    {
        $this->service = new DateTimeService();
    }

    public function init($request)
    {
        header('Content-Type: application/json');
        print json_encode($this->getResponseData($request));
    }

    public function getResponseData($request): array
    {
        $validator = new DateTimeAPIValidatorService();
        $validator->validateFromRequestData($request);
        if ($validator->errors) {
            return ["errors" => $validator->errors];
        }
        $calculations = $this->service->calculateFromAndToRanges($validator->state, $validator->from, $validator->to);
        return ["data" => $calculations];
    }

}