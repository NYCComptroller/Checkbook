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

use Drupal\data_controller\Common\Object\Exception\IllegalArgumentException;
use Drupal\data_controller\Environment\Environment;
use NumberFormatter;


abstract class AbstractNumberDataTypeHandler extends AbstractDataTypeHandler {

    private static $MAX_DIGIT_NUMBER = 14;

    public $decimalSeparator = NULL;
    protected $numberFormatter = NULL;

    public function __construct() {
        parent::__construct();
        $this->numberFormatter = new NumberFormatter(Environment::getInstance()->getLocale(), $this->getNumberStyle());
        $this->decimalSeparator = Environment::getInstance()->getNumericFormattingElement('decimal_point');
    }

    protected function getNumberStyle() {
        return NumberFormatter::DECIMAL;
    }

    protected function getNumberType() {
        return NumberFormatter::TYPE_DOUBLE;
    }

    public function parse($value) {
        $offset = 0;
        $n = $this->numberFormatter->parse($value, $this->getNumberType(), $offset);

        return ($n === FALSE) || ($offset != strlen($value))
            ? FALSE
            : $n;
    }

    protected function isParsableImpl(&$value) {
        if (!parent::isParsableImpl($value)) {
            return FALSE;
        }

        $adjustedValue = strtoupper($value);
        $adjustedValueLength = strlen($adjustedValue);

        $isNumber = ($this->parse($adjustedValue) !== FALSE);

        // GOVDB-284. It is correct number. Adding check to prevent possible rounding or converting to scientific format by PHP
        if ($isNumber && ($adjustedValueLength > self::$MAX_DIGIT_NUMBER)) {
            $count = 0;
            for ($i = 0; $i < $adjustedValueLength; $i++) {
                $char = $adjustedValue[$i];
                if (($char >= '0') && ($char <= '9')) {
                    $count++;
                }
            }
            if ($count > self::$MAX_DIGIT_NUMBER) {
                $isNumber = FALSE;
            }
        }

        return $isNumber;
    }

    protected function adjustValue($value) {
        $adjustedValue = parent::adjustValue($value);
        if (is_string($adjustedValue)) {
            $adjustedValue = str_replace(' ', '', $adjustedValue);
        }

        return $adjustedValue;
    }

    protected function errorCastValue($value) {
        throw new IllegalArgumentException(t("'@value' is not of data type @type", array('@type' => $this->getStorageDataType(), '@value' => $value)));
    }
}
