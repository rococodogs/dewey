<?php
class DeweyTest extends PHPUnit_Framework_TestCase {

    protected $callNumber = "514.123 A997x";

    public function testCalculateRange() {
        $this->assertEquals(
            array("740", "750"),
            Dewey::calculateRange("74x"),
            'Range works w/ ones-place X'
        );

        $this->assertEquals(
            array("700", "800"),
            Dewey::calculateRange("7xx"),
            'Range works w/ tens-place + ones-place Xs'
        );
    }

    public function testCalculateRangeDecimal() {
        $this->assertEquals(
            array("740.0", "741.0"),
            Dewey::calculateRange("740.x"),
            'Range affects ones-place when using single X'
        );

        $this->assertEquals(
            array("740.20", "740.30"),
            Dewey::calculateRange("740.2x"),
            'Range drills down to tenths'
        );

        $this->assertEquals(
            array("740.22", "750.22"),
            Dewey::calculateRange("74x.22"),
            'If X is before decimal, leaves decimals in range'
        );
    }

    public function testCompareEQEQ() {
        $this->assertTrue(Dewey::compare($this->callNumber, $this->callNumber, "=="));
    }

    public function testCompareEQEQEQ() {
        $this->assertTrue(Dewey::compare($this->callNumber, $this->callNumber, "==="));
    }

    public function testCompareGT() {
        $this->assertTrue(Dewey::compare($this->callNumber, "510", ">"));
        $this->assertTrue(Dewey::compare($this->callNumber, "514.12 A997w", ">"));
        $this->assertFalse(Dewey::compare($this->callNumber, "514.1230 A997x", ">"));
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
        $this->assertTrue(Dewey::compare($this->callNumber, "520", "<="));
        $this->assertTrue(Dewey::compare($this->callNumber, $this->callNumber, "<="));
    }

    public function testInRange() {
        $this->assertTrue(Dewey::inRange($this->callNumber, "5xx"));
        $this->assertTrue(Dewey::inRange($this->callNumber, "514.x"));
        $this->assertFalse(Dewey::inRange($this->callNumber, $this->callNumber, false));
    }
}