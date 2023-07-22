<?php

use Controller\DateTimeAPIController;
use Data\CalculatedResponseData;
use Tests\DateTimeTestBase;
use Tests\MockDateTimeService;

class DateTimeAPIControllerTest extends DateTimeTestBase
{

    public function runTests()
    {
        $this->testPostingToLiveEndpoint();
        $this->testGetResponseData();
        $this->outputResults();
    }

    /**
     * Live endpoint only needs 1 test to ensure it exists, the business logic is tested directly on a function by function basis
     */
    public function testPostingToLiveEndpoint()
    {
        $response = $this->postToEndpoint("http://localhost/Aligent/index.php", ["to" => '2022-10-10']);
        $expected = '{"errors":["stateForPublicHolidays is required and must be one of sa, nsw, vic","from is required"]}';
        $this->assertSame($expected, $response);
    }

    private function postToEndpoint($url, $data)
    {
        $jsonString = json_encode($data);
        $options = [
            'http' => [
                'header' => "Content-type: application/json\r\nContent-Length: " . strlen($jsonString),
                'method' => "POST",
                'content' => $jsonString,
                'ignore_errors' => true
            ]
        ];
        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $status_line = $http_response_header[0];
        preg_match('{HTTP\/\S*\s(\d{3})}', $status_line, $match);
        $status = $match[1];
        $this->assertSame(200, (int)$status);
        return $response;
    }

    public function testGetResponseData() {
        $controller = new DateTimeAPIController();
        $controller->service = new MockDateTimeService();

        // example of mocking out function (this can also be done in phpunit using it's mocking tool)
        $controller->service->overrideCalculateFromAndToRanges = new CalculatedResponseData("sa", new DateTime("2000-02-02"), new DateTime("2001-02-02"));

        // asserts errors get returned
        $response = $controller->getResponseData([]);
        $expected = '{"errors":["stateForPublicHolidays is required and must be one of sa, nsw, vic","to is required","from is required"]}';
        $this->assertSame($expected, json_encode($response));

        // asserts valid response gets returned
        $response = $controller->getResponseData(["stateForPublicHolidays" => "sa", "from" => "2022-01-01", "to" => "2023-01-01"]);
        $expected = '{
  "data": {
    "from": {
      "date": "2000-02-02 00:00:00.000000",
      "timezone_type": 3,
      "timezone": "Australia/Adelaide"
    },
    "to": {
      "date": "2001-02-02 00:00:00.000000",
      "timezone_type": 3,
      "timezone": "Australia/Adelaide"
    },
    "totalSeconds": 0,
    "totalMinutes": 0,
    "totalHours": 0,
    "totalDays": 0,
    "totalYears": 0,
    "daysSkippingPublicHolidays": 0,
    "daysSkippingWeekends": 0,
    "daysSkippingWeekendAndPublicHolidays": 0,
    "secondsSkippingPublicHolidays": 0,
    "secondsSkippingWeekends": 0,
    "secondsSkippingWeekendAndPublicHolidays": 0,
    "minutesSkippingPublicHolidays": 0,
    "minutesSkippingWeekends": 0,
    "minutesSkippingWeekendAndPublicHolidays": 0,
    "hoursSkippingPublicHolidays": 0,
    "hoursSkippingWeekends": 0,
    "hoursSkippingWeekendAndPublicHolidays": 0,
    "entireWeeksBetween": 0,
    "state": "sa"
  }
}';

        $this->assertSame(json_decode($expected, true), json_decode(json_encode($response), true));
    }

}
