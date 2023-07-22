Example URL:
http://localhost/Aligent/index.php?stateForPublicHolidays=sa&to=2024-01-01&from=2023-01-01

Can also be posted as JSON body to:
http://localhost/Aligent/index.php

Rather than use parameters in the request, the response returns all possibilities

Create a Web API that can be used to:
1. Find out the number of days between two datetime parameters.
2. Find out the number of weekdays between two datetime parameters.
3. Find out the number of complete weeks between two DateTime parameters.
4. Accept a third parameter to convert the result of (1, 2 or 3) into one of seconds,
   minutes, hours, years.
5. Allow the specification of a timezone for comparison of input parameters from different
   time zones.