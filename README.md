# Overview
I created this PHP code with simplicity in mind and without third-party libraries. I prioritized clear and descriptive naming conventions for classes, functions, and variables to ensure easy comprehension. When functions are well-written and concise, it minimizes the need for additional comments and documentation.

I made sure to write clean and concise code that is easy to follow and doesn't require extensive explanations. Additionally, I added the feature to skip public holidays in addition to weekends, and the response object returns all data at once without the need for different condition parameters.

## Unit Tests
My preferred method for unit testing involves the following steps, which may vary depending on the situation:
- I write each test to cover one function entirely, provided that the functions are small enough and would refactor those functions if they were too big.
- If a function calls a sub function, I consider using a mock to simulate the sub function.
- This approach ensures that tests do not need to cover conditions beyond the scope of the function being tested.
- Once all functions are tested in this manner, I have confidence that the unit tests reflect the true business logic.

## Folder Structure
- Controller, used to manage the API endpoint
- Data, classes representing data not connected to a database
- Service, services responsible for running business logic
- Tests, follows the same folder structure as above, but contains unit tests

## How to run

Run inside a php environment with localhost pointing to thsi folder.

Example URL:
> http://localhost/index.php?stateForPublicHolidays=sa&to=2024-01-01&from=2023-01-01

Can also be posted as JSON body to:
> http://localhost/index.php

Run Unit Tests:
> http://localhost/index.php?runTests
