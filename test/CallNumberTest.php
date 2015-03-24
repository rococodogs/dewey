<?php
class CallNumberTest extends PHPUnit_Framework_TestCase {

    protected $cn;
    protected $callNumberString = "514.123 A987x";
    protected $callNumber = "514.123";
    protected $cutter = "A987x";

    protected $normalized           = "5141230198724";
    protected $normalizedLessDigits = "5141000198724";
    protected $normalizedCNPadded   = "514123000198724";
    protected $normalizedCTPadded   = "51412301987240";
    protected $normalizedPadded     = "5141230001987240";

    public function setUp() {
        $this->cn = new Dewey\CallNumber;
        $this->cn->setCallNumber($this->callNumber);
        $this->cn->setCutter($this->cutter);
    }

    public function testToString() {
        $this->assertEquals(
            $this->callNumberString, 
            $this->cn . '',
            'CallNumber prints as a readable DDS call number'
        );
    }

    public function testCalculateNormalizedCutter() {
        $ct = "0198724";
        
        $this->assertEquals(
            $ct, 
            $this->cn->calculateNormalizedCutter(),
            'no padding'
        );
        
        $this->assertEquals(
            $ct, 
            $this->cn->calculateNormalizedCutter($this->cn->getNormalizedCutterLength() - 1),
            'slight pading'
        );

        $this->assertEquals(
            $ct . "00", 
            $this->cn->calculateNormalizedCutter($this->cn->getNormalizedCutterLength() + 2), 
            'noticeable padding'
        );
    }

    public function testCompare() {
        $this->assertTrue(
            $this->cn->compare($this->callNumberString, "=="),
            'Double equals should compare true'
        );
    }

    public function testEqualTo() {
        $this->assertTrue($this->cn->equalTo($this->cn));
    }

    public function testGetCallNumberLength() {
        $this->assertEquals(6, $this->cn->getCallNumberLength());
    }

    public function testGetCutterLength() {
        $this->assertEquals(
            5, 
            $this->cn->getCutterLength(),
            'Getting the cutter length should return not normalized length'
        );

        $this->assertEquals(
            7,
            $this->cn->getCutterLength(true),
            'Passing `true` to getCutterLength will calculate normalized cutter length'
        );
    }

    public function testGreaterThan() {
        $this->assertTrue(
            $this->cn->greaterThan($this->callNumber),
            'A DDS call number w/ a cutter will calculate greater than the same w/o'
        );
    }

    public function testGreaterThanEqualTo() {
        $this->assertTrue(
            $this->cn->greaterThan($this->callNumber),
            'A DDS call number w/ a cutter will calculate greater than the same w/o'
        );

        $this->assertTrue(
            $this->cn->greaterThanEqualTo($this->cn),
            'greaterThanEqualTo should equate true for equal to'
        );
    }

    public function testInRange() {
        $ranges = array("51x", "514.x", "514.1x", "514.12x", "514.123x");
        foreach($ranges as $range) {
            $this->assertTrue(
                $this->cn->inRange($range),
                'inRange string works with range string: [' . $range . ']'
            );
        }

        $this->assertTrue(
            $this->cn->inRange(array("510", "520")),
            'inRange works with range tuple'
        );
    }

    public function testInRangeLessThanMax() {
        $newCN = \Dewey::parseCallNumber("515");
        $this->assertTrue($newCN->inRange("514.x"));
        $this->assertFalse($newCN->inRange("514.x", false));
    }

    public function testLessThan() {
        $newCN = \Dewey::parseCallNumber($this->callNumber);
        $this->assertTrue(
            $newCN->lessThan($this->cn),
            'A DDS call number w/o a cutter will calculate less than the same with'
        );

        $this->assertTrue(
            $this->cn->lessThan("515"),
            'lessThan works with a single major number'
        );
        
        $this->assertFalse(
            $this->cn->lessThan($newCN),
            'instance CallNumber shouldn\'t be less than the test one'
        );
    }

    public function testLessThanEqualTo() {
        $this->assertTrue(
            $this->cn->lessThan("520"),
            'lessThanEqualTo a number larger than self should return true'
        );
        
        $this->assertTrue(
            $this->cn->lessThanEqualTo($this->cn),
            'lessThanEqualTo self should return true'
        );
    }

    public function testNormalized() {
        $this->assertEquals($this->normalized, $this->cn->calculateNormalized());
    }

    public function testNormalizedCallNumberPadding() {
        $this->assertEquals(
            $this->normalizedCNPadded,
            $this->cn->calculateNormalized(array('callNumber' => 8))
        );

        $this->cn->setCallNumber("514.1");
        $this->assertEquals(
            $this->normalizedLessDigits,
            $this->cn->calculateNormalized(array('callNumber' => 6))
        );
    }

    public function testNormalizedCutterPadding() {        
        $this->assertEquals(
            $this->normalizedCTPadded,
            $this->cn->calculateNormalized(array('cutter' => 8))
        );
    }

    public function testNormalizedPadding() {
        $this->assertEquals(
            $this->normalizedPadded,
            $this->cn->calculateNormalized(array('callNumber' => 8, 'cutter' => 8))
        );
    }
}