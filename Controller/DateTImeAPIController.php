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

    public function init()
    {
        $json = file_get_contents('php://input');
        if (!empty($json)) {
            $data = json_decode($json, true);
        } else {
            $data = $_REQUEST;
        }
        header('Content-Type: application/json');
        print json_encode($this->getResponseData($data));
    }

    public function getResponseData($data): array
    {
        $validator = new DateTimeAPIValidatorService();
        $validator->validateData($data);
        if ($validator->errors) {
            return ["errors" => $validator->errors];
        }
        $calculations = $this->service->calculateFromAndToRanges($validator->state, $validator->from, $validator->to);
        return ["data" => $calculations];
    }

}