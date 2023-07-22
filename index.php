<?php

use Controller\DateTimeAPIController;
use Tests\DateTimeAPIValidatorServiceTest;
use Tests\DateTimeServiceTest;

require_once"Controller/DateTimeAPIController.php";
require_once"Data/CalculatedResponseData.php";
require_once"Data/PublicHoliday.php";
require_once"Service/DateTimeAPIValidatorService.php";
require_once"Service/DateTimeService.php";

if (isset($_GET["runTests"])) {
    require_once"Tests/DateTimeTestBase.php";
    require_once"Tests/Service/MockDateTimeService.php";
    require_once"Tests/Service/DateTimeServiceTest.php";
    require_once"Tests/Service/DateTimeAPIValidatorServiceTest.php";
    require_once"Tests/Controller/DateTimeAPIControllerTest.php";

    (new DateTimeServiceTest())->runTests();
    (new DateTimeAPIValidatorServiceTest())->runTests();
    (new DateTimeAPIControllerTest())->runTests();
} else {
    $controller = new DateTimeAPIController();
    $controller->init();
}
