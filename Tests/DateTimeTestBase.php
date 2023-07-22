<?php

namespace Tests;

abstract class DateTimeTestBase
{

    public $failures = [];
    public $successCount = 0;

    abstract public function runTests();

    public function outputResults()
    {
        print "<p>Tests for " . get_class($this) . ":</p>";
        if ($this->failures) {
            print implode("<br>", $this->failures);
        } else {
            print $this->successCount . " unit tests passed";
        }
    }

    public function assertSame($expected, $actual)
    {
        if ($expected !== $actual) {
            if (is_array($expected)) {
                $expected = json_encode($expected);
            }
            if (is_array($actual)) {
                $actual = json_encode($actual);
            }
            if (is_bool($expected)) {
                $expected = $expected ? "true" : "false";
            }
            if (is_bool($actual)) {
                $actual = $actual ? "true" : "false";
            }
            $trace0 = debug_backtrace()[0];
            $trace1 = debug_backtrace()[1];
            $this->failures[] = "Expected " . $expected . ", Actual " . $actual . " at " . $trace1["function"] . " #" . $trace0["line"];
        } else {
            $this->successCount++;
        }
    }

}
