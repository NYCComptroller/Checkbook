<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class MySQLFormatDateValueImpl extends AbstractFormatDateValueImpl {

    protected function adjustMask($mask) {
        $adjustedMask = '';

        for ($i = 0, $len = strlen($mask); $i < $len; $i++) {
            $specifier = $mask[$i];

            $mysqlSpecifier = $specifier;
            switch ($specifier) {
                case 'Y':
                case 'm':
                case 'd':
                case 'H':
                case 'i':
                case 's':
                    $mysqlSpecifier = '%' . $specifier;
                    break;
            }

            $adjustedMask .= $mysqlSpecifier;
        }

        return $adjustedMask;
    }

    public function formatImpl(DataSourceHandler $handler, $formattedValue, $adjustedMask) {
        return "STR_TO_DATE($formattedValue, '$adjustedMask')";
    }
}
