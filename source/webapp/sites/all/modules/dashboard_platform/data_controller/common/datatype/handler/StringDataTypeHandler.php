<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
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
