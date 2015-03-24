<?php

class Dewey {

    const DDS_FULL_REGEX = "/(\d{1,3})\.?([^\s]*)?\s*([^\s]*)?\s*(.*)?/";

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