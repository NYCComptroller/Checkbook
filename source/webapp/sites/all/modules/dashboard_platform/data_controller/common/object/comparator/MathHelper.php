<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class MathHelper {

    public static function max() {
        $values = func_get_args();

        if ((count($values) === 1) && is_array($values[0])) {
            $values = $values[0];
        }

        $max = NULL;
        foreach ($values as $value) {
            if (!isset($value)) {
                continue;
            }

            if (is_array($value)) {
                throw new UnsupportedOperationException(t('Current implementation of MAX function does not support nested arrays'));
            }

            if (isset($max)) {
                $max = max($max, $value);
            }
            else {
                $max = $value;
            }
        }

        return $max;
    }

    public static function min() {
        $values = func_get_args();

        if ((count($values) === 1) && is_array($values[0])) {
            $values = $values[0];
        }

        $min = NULL;
        foreach ($values as $value) {
            if (!isset($value)) {
                continue;
            }

            if (is_array($value)) {
                throw new UnsupportedOperationException(t('Current implementation of MIN function does not support arrays'));
            }

            if (isset($min)) {
                $min = min($min, $value);
            }
            else {
                $min = $value;
            }
        }

        return $min;
    }
}
