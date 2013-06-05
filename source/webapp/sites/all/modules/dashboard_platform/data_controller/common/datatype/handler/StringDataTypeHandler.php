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


class StringDataTypeHandler extends AbstractStringDataTypeHandler {

    public static $DATA_TYPE = 'string';

    public static function checkValueAsPropertyName($value) {
        if (!isset($value)) {
            return;
        }

        $result = preg_match('/^[a-zA-Z_][\w\x2e]*$/', $value);
        if ($result === FALSE) {
            $lastError = preg_last_error();
            LogHelper::log_error(t(
                "'@value' could not be validated as property name: Regular expression error: @lastError",
                array('@value' => $value, '@lastError' => $lastError)));
        }
        elseif ($result == 0) {
            LogHelper::log_error(t("'@value' is not property name", array('@value' => $value)));
        }
        else {
            return;
        }

        throw new IllegalArgumentException(t('Value is not correct property name'));
    }

    public static function checkValueAsWord($value) {
        if (!isset($value)) {
            return;
        }

        $result = preg_match('/^[a-zA-Z_]\w*$/', $value);
        if ($result === FALSE) {
            $lastError = preg_last_error();
            LogHelper::log_error(t(
                "'@value' could not be validated as a word: Regular expression error: @lastError",
                array('@value' => $value, '@lastError' => $lastError)));
        }
        elseif ($result == 0) {
            LogHelper::log_error(t("'@value' is not a word", array('@value' => $value)));
        }
        else {
            return;
        }

        throw new IllegalArgumentException(t("'@value' is not a word", array('@value' => $value)));
    }

    public function getStorageDataType() {
        return self::$DATA_TYPE;
    }

    public function selectCompatible($datatype) {
        return self::$DATA_TYPE;
    }
}
