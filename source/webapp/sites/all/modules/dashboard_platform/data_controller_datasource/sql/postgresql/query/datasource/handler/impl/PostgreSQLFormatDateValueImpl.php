<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class PostgreSQLFormatDateValueImpl extends AbstractFormatDateValueImpl {

    protected function adjustMask($mask) {
        $adjustedMask = '';

        for ($i = 0, $len = strlen($mask); $i < $len; $i++) {
            $specifier = $mask[$i];

            $postgreSpecifier = $specifier;
            switch ($specifier) {
                case 'Y':
                    $postgreSpecifier = 'YYYY';
                    break;
                case 'm':
                    $postgreSpecifier = 'MM';
                    break;
                case 'd':
                    $postgreSpecifier = 'DD';
                    break;
                case 'H':
                    $postgreSpecifier = 'HH24';
                    break;
                case 'i':
                    $postgreSpecifier = 'MI';
                    break;
                case 's':
                    $postgreSpecifier = 'SS';
                    break;
            }

            $adjustedMask .= $postgreSpecifier;
        }

        return $adjustedMask;
    }

    public function formatImpl(DataSourceHandler $handler, $formattedValue, $adjustedMask) {
        return "TO_DATE($formattedValue, '$adjustedMask')";
    }
}
