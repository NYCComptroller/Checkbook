<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class URIDataTypeHandler extends AbstractStringDataTypeHandler {

    public static $DATA_TYPE = 'URI';

    public function getHandlerType() {
        return DATA_TYPE__BUSINESS;
    }

    public function getStorageDataType() {
        return StringDataTypeHandler::$DATA_TYPE;
    }

    protected function isValueOfImpl(&$value) {
        return parent::isValueOfImpl($value) && (filter_var($value, FILTER_VALIDATE_URL) !== FALSE);
    }

    protected function isParsableImpl(&$value) {
        return parent::isParsableImpl($value) && (filter_var($value, FILTER_VALIDATE_URL) !== FALSE);
    }

    public function castValue($value) {
        $adjustedValue = parent::castValue($value);
        if (!isset($adjustedValue)) {
            return NULL;
        }

        $adjustedValue = filter_var($adjustedValue, FILTER_VALIDATE_URL);
        if ($adjustedValue === FALSE) {
            throw new IllegalArgumentException(t("'@value' is not of type @type", array('@value' => $value, '@type' => self::$DATA_TYPE)));
        }

        return $adjustedValue;
    }
}
