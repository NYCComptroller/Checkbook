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
