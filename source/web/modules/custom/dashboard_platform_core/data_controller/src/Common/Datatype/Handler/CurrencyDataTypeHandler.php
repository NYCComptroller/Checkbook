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

class CurrencyDataTypeHandler extends AbstractNumberDataTypeHandler {

    public static $DATA_TYPE = 'currency';

    public static $SUFFIX_THOUSANDS = 'K';
    public static $SUFFIX_MILLIONS = 'M';
    public static $SUFFIX_BILLIONS = 'B';
    public static $SUFFIX_TRILLIONS = 'T';

    protected function getSuffixConfigurations() {
        return array(
            self::$SUFFIX_THOUSANDS => pow(10, 3),
            self::$SUFFIX_MILLIONS => pow(10, 6),
            self::$SUFFIX_BILLIONS => pow(10, 9),
            self::$SUFFIX_TRILLIONS => pow(10, 12));
    }

    protected function getNumberStyle() {
        return NumberFormatter::CURRENCY;
    }

    protected function getNumberType() {
        return NumberFormatter::TYPE_CURRENCY;
    }

    public function getStorageDataType() {
        return self::$DATA_TYPE;
    }

    public function parse($value) {
        $adjustedValue = $value;

        // checking if we have negative value sign
        // For the US correct negative format is ($10,789.34) but some systems do use -$10,789.34 or $-10,789.34 or etc. instead
        $negativeSignIndex = strpos($adjustedValue, '-');
        $isNegative = $negativeSignIndex !== FALSE;
        if ($isNegative) {
            // we replace ONLY first occurrence of '-'.
            // If the value contains several such occurrences it means the value is incorrect and the following parser will return FALSE
            $adjustedValue = substr_replace($adjustedValue, '', $negativeSignIndex, 1);
        }

        // checking for number in the thousands, millions, billions and etc.
        $detectedMultiplier = NULL;
        $suffixes = $this->getSuffixConfigurations();
        if (isset($suffixes)) {
            $selectedSuffix = NULL;

            foreach ($suffixes as $suffix => $multiplier) {
                $suffixIndex = strpos($adjustedValue, $suffix);
                if ($suffixIndex !== FALSE) {
                    // two suffixes cannot be supported
                    if (isset($selectedSuffix)) {
                        $selectedSuffix = NULL;
                        break;
                    }

                    $selectedSuffix = $suffix;
                }
            }

            if (isset($selectedSuffix)) {
                $suffixLength = strlen($selectedSuffix);
                $suffixIndex = strpos($adjustedValue, $selectedSuffix);
                // suffix should be at the end of value
                if (strlen($adjustedValue) == ($suffixIndex + $suffixLength)) {
                    // removing spaces before the suffix ... if any
                    $startingIndex = $suffixIndex;
                    while ($startingIndex > 0) {
                        if ($adjustedValue[$startingIndex - 1] == ' ') {
                            $startingIndex--;
                        }
                        else {
                            break;
                        }
                    }
                    $adjustedValue = substr_replace($adjustedValue, '', $startingIndex, $suffixIndex - $startingIndex + $suffixLength);

                    $detectedMultiplier = $suffixes[$selectedSuffix];
                }
            }
        }

        $adjustedValueLength = strlen($adjustedValue);

        $offset = 0;
        $n = $this->numberFormatter->parseCurrency($adjustedValue, $currencyName, $offset);
        if (($n === FALSE) || ($offset != $adjustedValueLength)) {
            return FALSE;
        }

        if (isset($detectedMultiplier)) {
            $n *= $detectedMultiplier;
        }

        if ($isNegative) {
            $n *= -1;
        }

        return $n;
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

    protected function isParsableImpl(&$value) {
        if (!parent::isParsableImpl($value)) {
            return FALSE;
        }

        $currencySymbol = Environment::getInstance()->getNumericFormattingElement('currency_symbol');

        return strpos($value, $currencySymbol) !== FALSE;
    }

    public function castValue($value) {
        $adjustedValue = parent::castValue($value);
        if (!isset($adjustedValue)) {
            return NULL;
        }

        // at first we try to cast to number. If that does not work we try to cast to currency
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
