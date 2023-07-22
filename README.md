# Overview
This is code is kept as simple and runnable as possible, no third party libraries, just PHP.

I am a fan of code that is written with clear class, variable and function names that describe their purpose. Including functions being written small and well enough to be easily understandable. In this way comments and documentation can be kept to a minimum.

Many comments and documentation don't necessarily improve poorly written code.

In addition to the original requirements, this code also adds the ability to skip over public holidays as well as weekends.

The response object will return all data at once, rather than passing in condition parameters to get different response types, I felt this honored the spirit of this programming test.

## Unit Tests
I like to unit test usually in the following manner (can change depending on context):
* Write each test to cover 1 function in it's entirety (functions are written small enough)
* If a function calls a sub function, consider mocking out the sub function
* This way a test doesn't need to test all conditions beyond the scope of the function it is hitting
* When all functions are tested with 100% in this manner it provides confidence that the unit tests are now the source of truth of the business logic

## Folder Structure
* Controller, used to manage the API endpoint
* Data, classes representing data not connected to a database
* Service, services responsible for running business logic
* Tests, follows the same folder structure as above, but contains unit tests

## How to run

Run inside a php environment with localhost pointing to thsi folder.

Example URL:
> http://localhost/index.php?stateForPublicHolidays=sa&to=2024-01-01&from=2023-01-01

Can also be posted as JSON body to:
> http://localhost/index.php

Run Unit Tests:
> http://localhost/index.php?runTests

