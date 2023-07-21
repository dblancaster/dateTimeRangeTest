<?php

use Controller\DateTimeAPIController;

include "Controller/DateTimeAPIController.php";
include "Data/CalculatedResponseData.php";
include "Data/PublicHoliday.php";
include "Service/DateTimeAPIValidatorService.php";
include "Service/DateTimeService.php";

// http://localhost/Aligent/index.php?state=sa&to=2024-01-01&from=2023-01-01&action=workingHoursBetween

$controller = new DateTimeAPIController();
$controller->init($_REQUEST);
