<?php
class CallNumberTest extends PHPUnit_Framework_TestCase {

    protected $cn;
    protected $callNumberString = "514.123 A987x";
    protected $callNumber = "514.123";
    protected $cutter = "A987x";

    protected $normalized =         "5141230198724";
    protected $normalizedLessDigits = "5141000198724";
    protected $normalizedCNPadded = "514123000198724";
    protected $normalizedCTPadded = "51412301987240";
    protected $normalizedPadded   = "514123000198724000";

    public function setUp() {
        $this->cn = new Dewey\CallNumber;
        $this->cn->setCallNumber($this->callNumber);
        $this->cn->setCutter($this->cutter);
    }

    public function testToString() {
        $this->assertEquals($this->callNumberString, $this->cn . '');
    }

    public function testGetCallNumberLength() {
        $this->assertEquals(6, $this->cn->getCallNumberLength());
    }

    public function testGetCutterLength() {
        $this->assertEquals(5, $this->cn->getCutterLength());
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
            $this->cn->calculateNormalized(array('cutter' => 6))
        );
    }

    public function testNormalizedPadding() {
        $this->assertEquals(
            $this->normalizedPadded,
            $this->cn->calculateNormalized(array('callNumber' => 8, 'cutter' => 8))
        );
    }
}