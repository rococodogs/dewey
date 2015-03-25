<?php
class DeweyTest extends PHPUnit_Framework_TestCase {

    protected $callNumber = "514.123 A997x";

    public function testCalculateRange() {
        $this->assertEquals(
            array("740", "750"),
            Dewey::calculateRange("74*"),
            'Range works w/ ones-place *'
        );

        $this->assertEquals(
            array("700", "800"),
            Dewey::calculateRange("7**"),
            'Range works w/ tens-place + ones-place *s'
        );
    }

    public function testCalculateRangeDecimal() {
        $this->assertEquals(
            array("740.0", "741.0"),
            Dewey::calculateRange("740.*"),
            'Range affects ones-place when using single *'
        );

        $this->assertEquals(
            array("740.20", "740.30"),
            Dewey::calculateRange("740.2*"),
            'Range drills down to tenths'
        );

        $this->assertEquals(
            array("740.22", "750.22"),
            Dewey::calculateRange("74*.22"),
            'If * is before decimal, leaves decimals in range'
        );
    }

    public function testCalculateRangeCutter() {
        $this->assertEquals(
            array("813 K5870", "813 K5880"),
            Dewey::calculateRange("813 K587*"),
            "Range should work with cutters" 
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
        $this->assertTrue(Dewey::inRange($this->callNumber, "5**"));
        $this->assertTrue(Dewey::inRange($this->callNumber, "514.*"));
        $this->assertFalse(Dewey::inRange($this->callNumber, $this->callNumber, false));
    }
}
