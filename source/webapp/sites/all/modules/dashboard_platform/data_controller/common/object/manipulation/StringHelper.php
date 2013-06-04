<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




class StringHelper {

    public static function trim($value) {
        if (isset($value)) {
            if (is_string($value)) {
                $value = trim($value);
                if (strlen($value) === 0) {
                    $value = NULL;
                }
            }
        }

        return $value;
    }

    public static function compareValues() {
        $values = func_get_args();
        if ((count($values) === 1) && is_array($values[0])) {
            $values = $values[0];
        }

        $selectedValue = NULL;
        $isValueSelected = FALSE;

        foreach ($values as $value) {
            $updatedValue = self::trim($value);

            if ($isValueSelected) {
                if (isset($selectedValue)) {
                    if (!isset($updatedValue) || ($selectedValue != $updatedValue)) {
                        return FALSE;
                    }
                }
                elseif (isset($updatedValue)) {
                    return FALSE;
                }
            }
            else {
                // selecting first value which is compared to
                $selectedValue = $updatedValue;
                $isValueSelected = TRUE;
            }
        }

        return TRUE;
    }

    public static function indent($s, $offset, $indentBlockStart) {
        $indent = str_pad('', $offset);

        return ($indentBlockStart ? $indent : '') . str_replace("\n", "\n$indent", $s);
    }
}
