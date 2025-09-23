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

use Drupal\data_controller\Environment\Environment;
use NumberFormatter;

class PercentDataTypeHandler extends AbstractNumberDataTypeHandler {

    public static $DATA_TYPE = 'percent';

    protected function getNumberStyle() {
        return NumberFormatter::PERCENT;
    }

    public function getStorageDataType() {
        return self::$DATA_TYPE;
    }

    protected function isValueOfImpl(&$value) {
        return parent::isValueOfImpl($value) && !is_string($value) && is_numeric($value) && !is_int($value);
    }

    public function selectCompatible($datatype) {
        if ($datatype === NumberDataTypeHandler::$DATA_TYPE) {
            return $datatype;
        }

        return parent::selectCompatible($datatype);
    }

    public function castValue($value) {
        $adjustedValue = parent::castValue($value);
        if (!isset($adjustedValue)) {
            return NULL;
        }

        // at first we try to cast to number. If that does not work we try to cast to percent
        $nf = new NumberFormatter(Environment::getInstance()->getLocale(), NumberFormatter::DECIMAL);
        $offset = 0; // we need to use $offset because in case of error parse() returns 0 instead of FALSE
        $n = $nf->parse($adjustedValue, NumberFormatter::TYPE_DOUBLE, $offset);
        if (($n === FALSE) || ($offset != strlen($adjustedValue))) {
            $n = $this->parse($adjustedValue);
        }
        if ($n === FALSE) {
            $this->errorCastValue($adjustedValue);
        }

        return $n;
    }
}
