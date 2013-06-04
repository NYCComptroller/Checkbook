<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class OracleFormatDateValueImpl extends AbstractFormatDateValueImpl {

    protected function adjustMask($mask) {
        $adjustedMask = '';

        for ($i = 0, $len = strlen($mask); $i < $len; $i++) {
            $specifier = $mask[$i];

            $oracleSpecifier = $specifier;
            switch ($specifier) {
                case 'Y':
                    $oracleSpecifier = 'YYYY';
                    break;
                case 'm':
                    $oracleSpecifier = 'MM';
                    break;
                case 'd':
                    $oracleSpecifier = 'DD';
                    break;
                case 'H':
                    $oracleSpecifier = 'HH24';
                    break;
                case 'i':
                    $oracleSpecifier = 'MI';
                    break;
                case 's':
                    $oracleSpecifier = 'SS';
                    break;
            }

            $adjustedMask .= $oracleSpecifier;
        }

        return $adjustedMask;
    }

    public function formatImpl(DataSourceHandler $handler, $formattedValue, $adjustedMask) {
        return "TO_DATE($formattedValue, '$adjustedMask')";
    }
}
