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

    protected $padding;

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
     *  calculate the normalized CallNumber object (a numerical representation of the full CallNumber)
     *  TODO: add normalized Additional support
     *  
     *  @param  mixed   amount of padding to assign to the CN
     *                  if numeric value, add that to callNumber, cutter, and additional values
     *                  if array, use values assigned to keys:
     *                      `callNumber`, `cutter`, `additional`
     *  @return string  normalized CallNumber value
     */

    public function calculateNormalized($padding = array()) {
        if ( !is_array($padding) ) {
            $val = intval($padding);
            $padding = array(
                'callNumber' => $val,
                'cutter' => $val,
                'additional' => $val
            );
        } else {
            $padding = array_merge(array(
                'callNumber' => 0,
                'cutter' => 0,
                'additional' => 0
            ), $padding);
        }

        $cnNorm = $this->calculateNormalizedCallNumber($padding['callNumber']);
        $ctNorm = $this->calculateNormalizedCutter($padding['cutter']);
        $adNorm = $this->calculateNormalizedAdditional($padding['additional']);

        return $cnNorm . $ctNorm . $adNorm;
    }

    /**
     *  calculates the 'normalized' call number (a numeric representation of the call number).
     *
     *      ex. `741.4372` becomes `7414372`
     *
     *  if $padding is provided, the string is zero-padded to contain that many digits
     *
     *      ex. `741.4372` with a padding of 9 becomes `741437200`
     *      ex. `741.4372` with a padding of 4 remains the same
     *
     *  @param  int/numeric  the minimum length of the string, the remaining spots are zero-padded
     *  @return string
     */

    public function calculateNormalizedCallNumber($padding = 0) {
        if ( !is_numeric($padding) ) { $padding = 0; }

        $this->padding['callNumber'] = $padding;

        $split = explode(".", $this->callNumber);
        $major = sprintf("%03d", $split[0]);
        $minor = isset($split[1]) ? $split[1] : "";
        $joined = $major . $minor;

        return str_pad($joined, $padding, "0", STR_PAD_RIGHT);
    }

    /**
     *  calculates the 'normalized' cutter (a numeric representation of the cutter).
     *
     *      ex. `A123x` becomes `0112324`
     *
     *  note: all letters are replaced with a two-digit number (`a` becomes `01`, `z` becomes `26`)
     *
     *  if $padding is provided, the string is zero-padded to contain that many digits
     *
     *      ex. `A12x` with a padding of 7 becomes `0112240`
     *
     *  @param  int/numeric  the minimum length of the string, the remaining spots are zero-padded
     *  @return string
     */

    public function calculateNormalizedCutter($padding = 0) {        
        if ( !is_numeric($padding) ) { $padding = 0; }
        $this->padding['cutter'] = $padding;

        $split = str_split($this->cutter);

        if ( empty($split) ) { return str_pad($this->cutter, $padding); }

        $normalized = "";

        foreach($split as $s) {
            if ( $s === "" || $s === " " ) { continue; }
            
            $pos = stripos("abcdefghijklmnopqrstuvwxyz", $s);
            if ( $pos === false ) { $normalized .= intval($s); }
            else { $normalized .= sprintf("%02d", $pos + 1); }
        }

        return str_pad($normalized, $padding, "0", STR_PAD_RIGHT);
    }

    /**
     *  TODO: implement the Additional Info field
     */

    public function calculateNormalizedAdditional($padding = 0) {
        //return str_pad("", $padding, "0");
        return "";
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
     *  (including cutter), b) just the call numbers (xxx.xxxx), or c) the normalized call number 
     *  length, the $normalized parameter may also be an array of options with the keys:
     *      `normalized`, and `includeCutter`
     *
     *  @param  mixed       array of options, or boolean to get length of normalized call number
     *  @param  boolean     if $normalized is _not_ an array, is a boolean to include the cutter in the length
     *  @return int
     */

    public function getCallNumberLength($normalized = false, $includeCutter = false) { 
        if ( is_array($normalized) ) {
            $opts = $normalized;
            $normalized = isset($opts['normalized']) ? $opts['normalized'] : false;
            $includeCutter = isset($opts['includeCutter']) ? $opts['includeCutter'] : false;
        }

        return !!$normalized
                ? strlen(str_replace(".", "", $this->callNumber))
                : $this->getNormalizedCallNumberLength($includeCutter)
                ; 
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
     *  returns the length of the cutter. if $normalized is true, will pad
     *  A-Z to two places (so that A == 01, Z == 26)
     *
     *  @param  boolean  whether to normalize letters (convert A-Z to two-digit number)
     *  @return int
     */

    public function getCutterLength($normalized = false) {
        if ( $normalized ) {
            return $this->getNormalizedCutterLength();
        }

        $c = preg_replace("/\s/", "", $this->cutter);
        return strlen($this->cutter);
    }

    /**
     *  retrieves the length of the 'normalized' call number length
     *
     *  @param  boolean     optional (default is `false`): whether to include the cutter in the length
     *  @return int 
     */

    public function getNormalizedCallNumberLength($includeCutter = false) {
        $l = strlen(str_replace(".", "", $this->callNumber));
        return !!$includeCutter 
                ? $l + $this->getNormalizedCutterLength()
                : $l
                ;
    }

    /**
     *  retrieves the length of the normalized cutter
     *
     *  @return int
     */

    public function getNormalizedCutterLength() {
        $c = preg_replace("/\s/", "", $this->cutter);
        return strlen(preg_replace("/[a-z]/i", "xx", $this->cutter));
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
        $this->additional = $additional;
    }

    /**
     *  setter for CallNumber::callNumber field
     *
     *  @param string
     */

    public function setCallNumber($cn) {
        $this->callNumber = $cn;
    }

    /**
     *  setter for CallNumber::cutter field
     *
     *  @param string
     */

    public function setCutter($ct) {
        $this->cutter = $ct;
    }

    /**
     *  join `callNumber`, `cutter`, and `additional` fields as a string
     *  when needed
     *
     *  @return string
     */

    public function __toString() {
        return trim($this->callNumber . " " . $this->cutter . " " . $this->additional);
    }
}