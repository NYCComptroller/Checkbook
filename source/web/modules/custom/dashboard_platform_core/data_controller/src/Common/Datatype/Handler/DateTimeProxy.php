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
use Drupal\data_controller\Common\Pattern\AbstractObject;


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
