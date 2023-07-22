<?php

use Controller\DateTimeAPIController;
use Tests\DateTimeAPIValidatorServiceTest;
use Tests\DateTimeServiceTest;

include "Controller/DateTimeAPIController.php";
include "Data/CalculatedResponseData.php";
include "Data/PublicHoliday.php";
include "Service/DateTimeAPIValidatorService.php";
include "Service/DateTimeService.php";

if (isset($_GET["runTests"])) {
    include "Tests/DateTimeTestBase.php";
    include "Tests/Service/MockDateTimeService.php";
    include "Tests/Service/DateTimeServiceTest.php";
    include "Tests/Service/DateTimeAPIValidatorServiceTest.php";
    include "Tests/Controller/DateTimeAPIControllerTest.php";

    (new DateTimeServiceTest())->runTests();
    (new DateTimeAPIValidatorServiceTest())->runTests();
    (new DateTimeAPIControllerTest())->runTests();
} else {
    $controller = new DateTimeAPIController();
    $controller->init($_REQUEST);
}
