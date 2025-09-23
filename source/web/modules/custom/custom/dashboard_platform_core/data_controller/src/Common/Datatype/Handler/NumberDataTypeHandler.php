<?php
/**
 * This file is part of the Checkbook NYC financial transparency software.
 *
 * Copyright (c) 2012 – 2023 New York City
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Drupal\data_controller\Common\Datatype\Handler;

class NumberDataTypeHandler extends AbstractNumberDataTypeHandler {

    public static $DATA_TYPE = 'number';

    public function getStorageDataType() {
        return self::$DATA_TYPE;
    }

    protected function isValueOfImpl(&$value) {
        return parent::isValueOfImpl($value) && !is_string($value) && is_numeric($value) && !is_int($value);
    }

    protected function isParsableImpl(&$value) {
        if (!parent::isParsableImpl($value)) {
            return FALSE;
        }

        $decimalSeparatorIndex = strpos($value, $this->decimalSeparator);

        // to support integer-like numbers such as 12345678901234 which cannot be mapped to integer32 type
        $isInteger = ($decimalSeparatorIndex === FALSE)
            ? $this->numberFormatter->parse($value, IntegerDataTypeHandler::NUMBER_TYPE)
            : FALSE;

        return (!$isInteger) && (($value[0] != '0') || ($decimalSeparatorIndex === 1));
    }

    public function castValue($value) {
        $adjustedValue = parent::castValue($value);
        if (!isset($adjustedValue)) {
            return NULL;
        }

        $n = $this->parse($adjustedValue);
        if ($n === FALSE) {
            $currency = new CurrencyDataTypeHandler();
            $n = $currency->parse($adjustedValue);
        }
        if ($n === FALSE) {
            $percent = new PercentDataTypeHandler();
            $n = $percent->parse($adjustedValue);
        }

        if ($n === FALSE) {
            $this->errorCastValue($adjustedValue);
        }

        return $n;
    }
}
