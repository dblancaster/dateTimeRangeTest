<?php

namespace Tests;

use Services\DateTimeAPIValidatorService;

class DateTimeAPIValidatorServiceTest extends DateTimeTestBase
{

    public DateTimeAPIValidatorService $service;

    public function runTests()
    {
        $this->testValidateData();
        $this->testValidateState();
        $this->testValidateFrom();
        $this->testValidateTo();
        $this->outputResults();
    }

    public function testValidateData()
    {
        $service = new DateTimeAPIValidatorService();
        $service->validateData([]);
        $expected = ["stateForPublicHolidays is required and must be one of sa, nsw, vic","to is required","from is required"];
        $this->assertSame($expected, $service->errors);
    }

    public function testValidateState()
    {
        $service = new DateTimeAPIValidatorService();

        $data = [];
        $service->validateState($data);
        $this->assertSame(["stateForPublicHolidays is required and must be one of sa, nsw, vic"], $service->errors);
        $this->assertSame(null, $service->state);

        $service->errors = [];
        $data = ["stateForPublicHolidays" => "test"];
        $service->validateState($data);
        $this->assertSame(["stateForPublicHolidays must be one of sa, nsw, vic"], $service->errors);
        $this->assertSame("test", $service->state);

        $service->errors = [];
        $data = ["stateForPublicHolidays" => "sa"];
        $service->validateState($data);
        $this->assertSame([], $service->errors);
        $this->assertSame("sa", $service->state);
    }

    public function testValidateTo() {
        $service = new DateTimeAPIValidatorService();

        $data = [];
        $service->validateTo($data);
        $this->assertSame(["to is required"], $service->errors);
        $this->assertSame(null, $service->to);

        $service->errors = [];
        $data["to"] = "invalid";
        $service->validateTo($data);
        $this->assertSame("invalid", $service->to);
        $this->assertSame(["Invalid Date Time format for 'to'"], $service->errors);

        $service->errors = [];
        $data["to"] = "2023-01-01 12:03:22";
        $service->validateTo($data);
        $this->assertSame([], $service->errors);
        $expected = '{"date":"2023-01-01 12:03:22.000000","timezone_type":3,"timezone":"Australia\/Adelaide"}';
        $this->assertSame($expected, json_encode($service->to));

        $service->errors = [];
        $data["toTimezone"] = "abc";
        $service->validateTo($data);
        $this->assertSame(["to timezone must in a format similar to Australia/Adelaide"], $service->errors);
        $this->assertSame(json_encode("2023-01-01 12:03:22"), json_encode($service->to));

        $service->errors = [];
        $data["toTimezone"] = "Australia/Sydney";
        $service->validateTo($data);
        $this->assertSame([], $service->errors);
        $expected = '{"date":"2023-01-01 12:03:22.000000","timezone_type":3,"timezone":"Australia\/Sydney"}';
        $this->assertSame($expected, json_encode($service->to));
    }

    public function testValidateFrom() {
        $service = new DateTimeAPIValidatorService();

        $data = [];
        $service->validateFrom($data);
        $this->assertSame(["from is required"], $service->errors);
        $this->assertSame(null, $service->from);

        $service->errors = [];
        $data["from"] = "invalid";
        $service->validateFrom($data);
        $this->assertSame("invalid", $service->from);
        $this->assertSame(["Invalid Date Time format for 'from'"], $service->errors);

        $service->errors = [];
        $data["from"] = "2023-01-01 12:03:22";
        $service->validateFrom($data);
        $this->assertSame([], $service->errors);
        $expected = '{"date":"2023-01-01 12:03:22.000000","timezone_type":3,"timezone":"Australia\/Adelaide"}';
        $this->assertSame($expected, json_encode($service->from));

        $service->errors = [];
        $data["fromTimezone"] = "abc";
        $service->validateFrom($data);
        $this->assertSame(["from timezone must in a format similar to Australia/Adelaide"], $service->errors);
        $this->assertSame(json_encode("2023-01-01 12:03:22"), json_encode($service->from));

        $service->errors = [];
        $data["fromTimezone"] = "Australia/Sydney";
        $service->validateFrom($data);
        $this->assertSame([], $service->errors);
        $expected = '{"date":"2023-01-01 12:03:22.000000","timezone_type":3,"timezone":"Australia\/Sydney"}';
        $this->assertSame($expected, json_encode($service->from));
    }

}
