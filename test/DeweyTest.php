<?php
class DeweyTest extends PHPUnit_Framework_TestCase {

    protected $callNumber = "514.123 A997x";

    public function testCompareEQEQ() {
        $this->assertTrue(Dewey::compare($this->callNumber, $this->callNumber, "=="));
    }

    public function testCompareGT() {
        $this->assertTrue(Dewey::compare($this->callNumber, "510", ">"));
        $this->assertTrue(Dewey::compare($this->callNumber, "514.12 A997w", ">"));
        $this->assertFalse(Dewey::compare($this->callNumber, "610.123 L019l", ">"));
        $this->assertFalse(Dewey::compare($this->callNumber, $this->callNumber, ">"));
    }

    public function testCompareGTEQ() {
        $this->assertTrue(Dewey::compare($this->callNumber, $this->callNumber, ">="));
        $this->assertTrue(Dewey::compare($this->callNumber, "514", ">="));
    }

    public function testCompareLT() {
        $this->assertTrue(Dewey::compare($this->callNumber, "514.2 A998a", "<"));
        $this->assertFalse(Dewey::compare($this->callNumber, "384.664223 G067m", "<"));
    }
    public function testCompareLTEQ() {
        $this->assertTrue(Dewey::compare($this->callNumber, "514.123 A998a", "<="));
        $this->assertTrue(Dewey::compare($this->callNumber, $this->callNumber, "<="));
    }
}