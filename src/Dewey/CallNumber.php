<?php
/**
 *  Dewey\CallNumber
 */

namespace Dewey;

class CallNumber {

    protected $callNumber;
    protected $cutter;

    protected $additional;

    protected $volume;
    protected $year;

    protected $padding;

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
        // $adNorm = $this->calculateNormalizedAdditional($padding['additional']);
        $adNorm = "";

        return $cnNorm . $ctNorm . $adNorm;
    }

    public function calculateNormalizedCallNumber($padding = 0) {
        if ( !is_numeric($padding) ) { $padding = 0; }

        $this->padding['callNumber'] = $padding;

        $split = explode(".", $this->callNumber);
        $major = sprintf("%03d", $split[0]);
        $minor = isset($split[1]) ? $split[1] : "";
        $joined = $major . $minor;

        return str_pad($joined, $padding, "0", STR_PAD_RIGHT);
    }

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
     *
     */

    public function calculateNormalizedAdditional($padding = 0) {
        return str_pad("", $padding, "0");
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

    public function equalTo($comp, $deep = false) {
        return $this->compare($comp, (!!$deep ? "===" : "=="));
    }

    public function getCallNumber() { 
        return $this->callNumber; 
    }

    public function getCallNumberLength($normalized = false, $includeCutter = false) { 
        return !!$normalized
                ? strlen(str_replace(".", "", $this->callNumber))
                : $this->getNormalizedCallNumberLength($includeCutter)
                ; 
    }

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

    public function getNormalizedCallNumberLength($includeCutter = false) {
        $l = strlen(str_replace(".", "", $this->callNumber));
        return !!$includeCutter 
                ? $l + $this->getNormalizedCutterLength()
                : $l
                ;
    }

    public function getNormalizedCutterLength() {
        $c = preg_replace("/\s/", "", $this->cutter);
        return strlen(preg_replace("/[a-z]/i", "xx", $this->cutter));
    }

    public function greaterThan($comp) {
        return $this->compare($comp, ">");
    }

    public function greaterThanEqualTo($comp) {
        return $this->compare($comp, ">=");
    }

    /**
     *  does this call number fall between the ranges specified? can use
     *  DDS call number string or a Dewey\CallNumber object
     *
     *  @param  mixed   either a min call number or a tuple w/ min and max
     *  @param  mixed   optional max call number (not used if $min is a tuple)
     *  @param  boolean whether to equate the max as lessThan or lessThanEqualTo (true for lessThan; false for LTEQ)
     *  @return boolean 
     */

    public function inRange($min, $max = null, $lessThanMax = true) {
        if ( is_array($min) ) {
            $max = $min[1];
            $min = $min[0];
        }

        if ( is_bool($max) ) {
            $lessThanMax = $max;
        }

        return $this->greaterThanEqualTo($min) 
            && ($lessThanMax ? $this->lessThan($max) : $this->lessThanEqualTo($max))
            ;
    }

    public function lessThan($comp) {
        return $this->compare($comp, "<");
    }

    public function lessThanEqualTo($comp) {
        return $this->compare($comp, "<=");
    }

    public function setAdditional($additional) {
        $this->additional = $additional;
    }

    public function setCallNumber($cn) {
        $this->callNumber = $cn;
    }

    public function setCutter($ct) {
        $this->cutter = $ct;
    }

    public function __toString() {
        return trim($this->callNumber . " " . $this->cutter . " " . $this->additional);
    }
}