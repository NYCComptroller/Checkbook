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

use DateTime;
use Drupal\data_controller\Common\Object\Exception\IllegalArgumentException;
use Exception;

abstract class AbstractDateDataTypeHandler extends AbstractDataTypeHandler {

    protected function isValueOfImpl(&$value) {
        // PHP does not support 'date' type yet :(
        return FALSE;
    }

    protected function isDateSeparatorPresent(&$characterUsage, $separator) {
        $separatorCode = ord($separator);

        // date separator should be present for at least two times
        return isset($characterUsage[$separatorCode]) && ($characterUsage[$separatorCode] >= 2);
    }

    protected function isParsableImpl(&$value) {
        if (!parent::isParsableImpl($value)) {
            return FALSE;
        }

        // Supported formats: m/d/y, d m y, d-m-y and d.m.y [plus optional time]
        $MIN_LENGTH_DATE = 6; // at least: day + separator + month + separator + year[2]
        if (strlen($value) < $MIN_LENGTH_DATE) {
            return FALSE;
        }

        // We need at least two '/', '-', '.' or ' ' to proceed
        $characterUsage = count_chars($value, 1);
        if (!$this->isDateSeparatorPresent($characterUsage, ' ')
                && !$this->isDateSeparatorPresent($characterUsage, '/')
                && !$this->isDateSeparatorPresent($characterUsage, '-')
                && !$this->isDateSeparatorPresent($characterUsage, '.')) {
            return FALSE;
        }

        return date_create($value) !== FALSE;
    }

    public function castValue($value) {
        $adjustedValue = parent::castValue($value);
        if (!isset($adjustedValue)) {
            return NULL;
        }

        // do not use procedural style. We need an exception in case of error
        try  {
            $dt = new DateTime($adjustedValue);
        }
        catch (Exception $e) {
            //LogHelper::log_error($e);
            throw new IllegalArgumentException(t('Failed to parse date and/or time string: @value', array('@value' => $adjustedValue)));
        }

        return $dt->format($this->getMask());
    }
}
