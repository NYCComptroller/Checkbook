<?php
/**
* This file is part of the Checkbook NYC financial transparency software.
* 
* Copyright (C) 2012, 2013 New York City
* 
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU Affero General Public License as
* published by the Free Software Foundation, either version 3 of the
* License, or (at your option) any later version.
* 
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU Affero General Public License for more details.
* 
* You should have received a copy of the GNU Affero General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
