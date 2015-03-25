<?php
/**
 *  Dewey\CallNumber
 */

namespace Dewey;

class CallNumber {

    /**
     *  stores the call number and the cutter fields
     */

    protected $callNumber;
    protected $cutter;

    /**
     *  currently, nothing's being done w/ the CallNumber::additional field. maybe sometime
     *  in the future?
     */

    protected $additional;

    /**
     *  it might be useful to split the CallNumber::additional field into
     *  sub-fields. these might be some of them
     */

    // protected $copy;
    // protected $volume;
    // protected $year;

    /**
     *  wrap the Dewey::parseCallNumber into the Dewey\CallNumber's constructor, so
     *  that anything that can be done with the Dewey class can be done with our object.
     *  otherwise, an empty object is returned
     *  
     *  @param  string              optional DDS call number string
     *  @return Dewey\CallNumber
     */

    public function __construct($callNumber = null) {
        if ( !is_null($callNumber) ) {
            return \Dewey::parseCallNumber($callNumber);
        }
    }

    /**
     *  compares instance CallNumber against given call number (string or object)
     *  by $operator
     *
     *  @param  mixed    call number to compare against (string or Dewey\CallNumber)
     *  @param  string   operator to use in comparison
     *  @return boolean  whether instance obj compares to given call number by $operator
     */

    public function compare($comp, $operator) {
        return \Dewey::compare($this, $comp, $operator);
    }

    /**
     *  wrapper to call CallNumber::compare with an equal-to operator
     *
     *  @param  mixed       call number to compare against (string or Dewey\CallNumber)
     *  @param  boolean     use deep equality? (true for yes, false (default) to not)
     *  @return boolean  whether instance obj compares to given call number by $operator
     */

    public function equalTo($comp, $deep = false) {
        return $this->compare($comp, (!!$deep ? "===" : "=="));
    }

    /**
     *  gets the DDS call number (w/o cutter + additional fields)
     *
     *  @return string
     */

    public function getCallNumber() { 
        return $this->callNumber; 
    }

    /**
     *  return the length of the call number. since this may refer to a) the CallNumber as a whole
     *  (including cutter), or b) just the call numbers (xxx.xxxx), the option to include the cutter
     *  is included as the sole parameter
     *
     *  @param  boolean     include the cutter in the length?
     *  @return int
     */

    public function getCallNumberLength($includeCutter = false) { 

        return strlen(
                str_replace(
                    array(".", " "), 
                    "", 
                    $this->callNumber . 
                    (!!$includeCutter ? $this->cutter : "")
                )
               ); 
    }

    /**
     *  gets the CallNumber's cutter value
     *
     *  @return string
     */

    public function getCutter() { 
        return $this->cutter; 
    }

    /**
     *  returns the length of the cutter.
     *
     *  @return int
     */

    public function getCutterLength() {
        return strlen($this->cutter);
    }

    /**
     *  getter for the CallNumber::prestamp field
     *
     *  @return string
     */

    public function getPrestamp() {
        return $this->prestamp;
    }

    /**
     *  wrapper for CallNumber::compare using the greater-than operator
     *
     *  @param  mixed    call number to compare against (string or Dewey\CallNumber)
     *  @return boolean  whether instance obj is greater-than input
     */

    public function greaterThan($comp) {
        return $this->compare($comp, ">");
    }

    /**
     *  wrapper for CallNumber::compare using the greater-than-equal-to operator
     *
     *  @param  mixed    call number to compare against (string or Dewey\CallNumber)
     *  @return boolean  whether instance obj is greater-than-equal-to input
     */

    public function greaterThanEqualTo($comp) {
        return $this->compare($comp, ">=");
    }

    /**
     *  does this call number fall between the ranges specified? can use
     *  DDS call number string or a Dewey\CallNumber object
     *
     *  @param  mixed       either a range string (using `x` to denote the area of range, or a tuple of min/max)
     *  @param  boolean     whether to include $max in equation  (true for LTEQ, false for LT)
     *  @return boolean 
     */

    public function inRange($range, $lessThanEqualTo = true) {
        if ( is_string($range) ) {
            $range = \Dewey::calculateRange($range);
        }

        list($min, $max) = $range;

        return $this->greaterThanEqualTo($min) 
            && (!!$lessThanEqualTo === true 
                ? $this->lessThanEqualTo($max)
                : $this->lessThan($max) 
            )
            ;
    }

    /**
     *  wrapper for CallNumber::compare using the less-than operator
     *
     *  @param  mixed    call number to compare against (string or Dewey\CallNumber)
     *  @return boolean  whether instance obj is less-than input
     */

    public function lessThan($comp) {
        return $this->compare($comp, "<");
    }

    /**
     *  wrapper for CallNumber::compare using the less-than-equal-to operator
     *
     *  @param  mixed    call number to compare against (string or Dewey\CallNumber)
     *  @return boolean  whether instance obj is less-than-equal-to input
     */

    public function lessThanEqualTo($comp) {
        return $this->compare($comp, "<=");
    }

    /**
     *  setter for the CallNumber::additional field
     *  TODO: currently not implemented; might want to parse on input
     *
     *  @param string
     */

    public function setAdditional($additional) {
        $this->additional = trim($additional);
    }

    /**
     *  setter for CallNumber::callNumber field
     *
     *  @param string
     */

    public function setCallNumber($cn) {
        $this->callNumber = trim($cn);
    }

    /**
     *  setter for CallNumber::cutter field
     *
     *  @param string
     */

    public function setCutter($ct) {
        $this->cutter = trim($ct);
    }

    /**
     *  setter for the CallNumber::prestamp field
     *
     *  @param string
     */

    public function setPrestamp($ps) {
        $this->prestamp = trim($ps);
    }

    /**
     *  join `callNumber`, `cutter`, and `additional` fields as a string
     *  when needed
     *
     *  @return string
     */

    public function __toString() {
        return implode(" ", array($this->preStamp, $this->callNumber, $this->cutter, $this->additional);
    }
}
