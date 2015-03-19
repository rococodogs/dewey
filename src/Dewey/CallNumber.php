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


    public function getCallNumber() { 
        return $this->callNumber; 
    }

    public function getCallNumberLength() { 
        return strlen(str_replace(".", "", $this->callNumber)); 
    }

    public function getCutter() { 
        return $this->cutter; 
    }

    public function getCutterLength() {
        return strlen(preg_replace("/\s/", "", $this->cutter));
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

    // public function setCallNumber($callNumber) {
    //     $this->callNumber = $callNumber;
    // }

    // public function setCutter($cutter) {
    //     $this->cutter = preg_replace("/\s/", "", $cutter);
    // }

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

        $remainder = $padding - $this->getCutterLength();

        return $remainder > 0 ? $normalized . str_repeat("0", $remainder) : $normalized;
    }

    /**
     *  TODO: implement the Additional Info field
     *
     */

    public function calculateNormalizedAdditional($padding = 0) {
        return str_pad("", $padding, "0");
    }


}