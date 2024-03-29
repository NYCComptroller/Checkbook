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

use Drupal\data_controller\Common\Datatype\DataTypeHandler;
use Drupal\data_controller\Common\Pattern\AbstractObject;

abstract class AbstractDataTypeHandler extends AbstractObject implements DataTypeHandler {

    public function getHandlerType() {
        return DATA_TYPE__PRIMITIVE;
    }

    public function getMask() {
        return NULL;
    }

    public function getStorageMask() {
        return NULL;
    }

    public final function isValueOf($value) {
        $adjustedValue = $this->adjustValue($value);

        return $this->isValueOfImpl($adjustedValue);
    }

    protected function isValueOfImpl(&$value) {
        return isset($value);
    }

    public function selectCompatible($datatype) {
        return NULL;
    }

    public final function isParsable($value) {
        $adjustedValue = $this->adjustValue($value);

        return $this->isParsableImpl($adjustedValue);
    }

    protected function isParsableImpl(&$value) {
        return isset($value);
    }

    public function castValue($value) {
        return $this->adjustValue($value);
    }

    protected function adjustValue($value) {
        if (!isset($value)) {
            return NULL;
        }

        $adjustedValue = $value;
        if (is_string($adjustedValue)) {
            $adjustedValue = trim($adjustedValue);
            if (strlen($adjustedValue) === 0) {
                return NULL;
            }

            $v = strtoupper($adjustedValue);
            //(Reverting change made earlier as this might impact cityiwde and edc transactions.
            // The issue is handled at individual filter and nycha json transaction settings)).
            if (($v === 'NULL') || ($v === 'N/A'))
            {
                return NULL;
            }
            //Handle case where we want to show N/A in the results
            if (($v === 'NOT_APPLICABLE_COLUMN')) {
                return 'N/A';
            }
        }

        return $adjustedValue;
    }
}
