<?php

class Dewey {

    const DDS_FULL_REGEX = "/(\d{1,3})\.?([^\s]*)?\s*([^\s]*)?\s*(.*)?/";

    /**
     *  calculates range based on x-substituted strings, return as a tuple array
     *
     *      ex. "74x" will return ["740", "750"]
     *      ex. "7xx" will return ["700", "800"]
     *
     *  when using a DDS minor will calculate w/ the decimal, but probably not necessary
     * 
     *      ex. "74x.22" will return ["740.22", "750.22"]
     *
     *  and will _not_ work w/ cutters, as our placeholder (`x`) may be used w/in a cutter
     */

    public static function calculateRange($rangeStr) {
        $min = "";
        $max = "";

        $decimalLocation = stripos($rangeStr, ".");

        // master number w/o decimal (we'll replace it later)
        $master = preg_replace(array("/\./", "/\s/"), "", $rangeStr);
        $length = strlen($master);
        $lastCharPlace = $length - 1;
        $xPos = array();

        for ( $i = 0; $i < $length; $i++ ) {
            $char = $master[$i];

            // any numeric, space, or period character gets added automatically
            if ( preg_match("/[0-9]/", $char) ) {
                $min .= $char;
                $max .= $char;
            }

            elseif ( preg_match("/x/i", $char) ) {
                // if we're at the first character, we need to stuff these values
                if ( $i === 0 ) {
                    $min .= "0";
                    $max .= "1";
                    continue;
                } 

                $prevChar = $master[$i - 1];
                if ( $prevChar !== "x" && $prevChar !== "X" ) {
                    $num = intval($prevChar);
                    $max[$i - 1] = ++$num;
                }

                $min .= "0";
                $max .= "0";
            }
        }

        if ( $decimalLocation !== false ) {
            $min = substr($min, 0, $decimalLocation) . "." . substr($min, $decimalLocation);
            $max = substr($max, 0, $decimalLocation) . "." . substr($max, $decimalLocation);
        }

        return array($min, $max);
    }

    public static function compare($input, $comp, $operator) {
        if ( !is_a($input, "Dewey\CallNumber") ) {
            $input = self::parseCallNumber($input);
        }

        if ( !is_a($comp, "Dewey\CallNumber") ) {
            $comp = self::parseCallNumber($comp);
        }

        // check for longest field to pad
        $inputCNLength = $input->getNormalizedCallNumberLength();
        $compCNLength = $comp->getNormalizedCallNumberLength();
        $inputCTLength = $input->getNormalizedCutterLength();
        $compCTLength = $comp->getNormalizedCutterLength();

        $padding = array(
            'callNumber' => $inputCNLength > $compCNLength ? $inputCNLength : $compCNLength,
            'cutter'     => $inputCTLength > $compCTLength ? $inputCTLength : $compCTLength
        );

        $inputNormalized = $input->calculateNormalized($padding);
        $compNormalized = $comp->calculateNormalized($padding);

        switch($operator) {
            case ">":  return $inputNormalized >  $compNormalized;
            case "<":  return $inputNormalized <  $compNormalized;
            case "<=": return $inputNormalized <= $compNormalized;
            case ">=": return $inputNormalized >= $compNormalized;
            case "==": return $inputNormalized == $compNormalized;

            // unneccessary but available
            case "===": return $inputNormalized === $compNormalized;
            default: throw new Exception("Unrecognized operator: [{$operator}]");
        }
    }

    public static function parseCallNumber($ddString) {
        preg_match(self::DDS_FULL_REGEX, $ddString, $matches);

        // handle bad Call Number
        if ( empty($matches) ) { throw new Exception("Malformed Dewey Decimal call number"); }

        $major = $matches[1];
        $minor = $matches[2];
        $cutter = $matches[3];
        $additionalInfo = $matches[4];

        $cn = new Dewey\CallNumber;
        $cn->setCallNumber($major . $minor);
        $cn->setCutter($cutter);
        $cn->setAdditional($additionalInfo);

        return $cn;
    }
}