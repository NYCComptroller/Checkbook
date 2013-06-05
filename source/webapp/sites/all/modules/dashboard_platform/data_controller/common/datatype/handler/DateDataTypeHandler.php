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


class DateTimeProxy extends AbstractObject {

    private $datetime = NULL;
    private $year = NULL;
    private $quarter = NULL;
    private $month = NULL;
    private $dayOfWeek = NULL;

    public function __construct(DateTime $datetime) {
        parent::__construct();
        if ($datetime === FALSE) {
            throw new IllegalArgumentException(t('Invalid date and/or time value'));
        }

        $this->datetime = $datetime;
    }

    public function getYear() {
        if (!isset($this->year)) {
            $this->year = (int) $this->datetime->format('Y');
        }

        return $this->year;
    }

    public function getQuarter() {
        if (!isset($this->quarter)) {
            $this->quarter = self::getQuarterByMonth($this->getMonth());
        }

        return $this->quarter;
    }

    public static function getQuarterByMonth($month) {
        return (int) ($month - 1) / 3 + 1;
    }

    public static function getFirstMonthOfQuarter($quarter) {
        return ($quarter - 1) * 3 + 1;
    }

    public function getMonth() {
        if (!isset($this->month)) {
            $this->month = (int) $this->datetime->format('m');
        }

        return $this->month;
    }

    public function getDayOfWeek() {
        if (!isset($this->dayOfWeek)) {
            $this->dayOfWeek = $this->datetime->format('D');
        }

        return $this->dayOfWeek;
    }
}


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
            LogHelper::log_error($e);
            throw new IllegalArgumentException(t('Failed to parse date and/or time string: @value', array('@value' => $adjustedValue)));
        }

        return $dt->format($this->getMask());
    }
}

class DateDataTypeHandler extends AbstractDateDataTypeHandler {

    public static $DATA_TYPE = 'date';

    public static $MASK_DEFAULT = 'm/d/Y';
    public static $MASK_CUSTOM = NULL;
    public static $MASK_STORAGE = 'm/d/Y';

    public static function getDateMask() {
        return isset(self::$MASK_CUSTOM) ? self::$MASK_CUSTOM : self::$MASK_DEFAULT;
    }

    public function getMask() {
        return self::getDateMask();
    }

    public static function getDateStorageMask() {
        return self::$MASK_STORAGE;
    }

    public function getStorageMask() {
        return self::getDateStorageMask();
    }

    public function getStorageDataType() {
        return self::$DATA_TYPE;
    }

    public function selectCompatible($datatype) {
        return ($datatype == DateTimeDataTypeHandler::$DATA_TYPE)
            ? DateTimeDataTypeHandler::$DATA_TYPE
            : parent::selectCompatible($datatype);
    }

    protected function isParsableImpl(&$value) {
        if (!parent::isParsableImpl($value)) {
            return FALSE;
        }


        // do not use class style. We do not need an exception to be thrown
        $info = date_parse($value);

        return ($info !== FALSE)
            && ($info['year'] !== FALSE) && ($info['month'] !== FALSE) && ($info['day'] !== FALSE)
            && ($info['hour'] === FALSE) && ($info['minute'] === FALSE) && ($info['second'] === FALSE);
    }
}

class TimeDataTypeHandler extends AbstractDateDataTypeHandler {

    public static $DATA_TYPE = 'time';

    public static $MASK_DEFAULT = 'h:i:s a';
    public static $MASK_CUSTOM = NULL;
    public static $MASK_STORAGE = 'H:i:s';

    public function getMask() {
        return isset(self::$MASK_CUSTOM) ? self::$MASK_CUSTOM : self::$MASK_DEFAULT;
    }

    public function getStorageMask() {
        return self::$MASK_STORAGE;
    }

    public function getStorageDataType() {
        return self::$DATA_TYPE;
    }

    public function selectCompatible($datatype) {
        return ($datatype == DateTimeDataTypeHandler::$DATA_TYPE)
            ? DateTimeDataTypeHandler::$DATA_TYPE
            : parent::selectCompatible($datatype);
    }

    protected function isParsableImpl(&$value) {
        if (!parent::isParsableImpl($value)) {
            return FALSE;
        }

        // do not use class style. We do not need an exception to be thrown
        $info = date_parse($value);

        return ($info !== FALSE)
            && ($info['year'] === FALSE) && ($info['month'] === FALSE) && ($info['day'] === FALSE)
            && ($info['hour'] !== FALSE) && ($info['minute'] !== FALSE) && ($info['second'] !== FALSE);
    }
}

class DateTimeDataTypeHandler extends AbstractDateDataTypeHandler {

    public static $DATA_TYPE = 'datetime';

    public static $MASK_DEFAULT = 'm/d/Y h:i:s a';
    public static $MASK_CUSTOM = NULL;
    public static $MASK_STORAGE = 'm/d/Y H:i:s';

    public function getMask() {
        return isset(self::$MASK_CUSTOM) ? self::$MASK_CUSTOM : self::$MASK_DEFAULT;
    }

    public function getStorageMask() {
        return self::$MASK_STORAGE;
    }

    public function getStorageDataType() {
        return self::$DATA_TYPE;
    }

    protected function isParsableImpl(&$value) {
        if (!parent::isParsableImpl($value)) {
            return FALSE;
        }

        // do not use class style. We do not need an exception to be thrown
        $info = date_parse($value);

        return ($info !== FALSE)
            && ($info['year'] !== FALSE) && ($info['month'] !== FALSE) && ($info['day'] !== FALSE)
            && ($info['hour'] !== FALSE) && ($info['minute'] !== FALSE) && ($info['second'] !== FALSE);
    }
}
