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

namespace Drupal\data_controller\Common\Object\Comparator\Handler;

use Drupal\data_controller\Common\Pattern\AbstractObject;

abstract class __PropertyBasedComparator_AbstractSortingConfiguration extends AbstractObject {

    public static $SORT_DIRECTION_DELIMITER__DESCENDING = '-';

    public $propertyName = NULL;
    public $isSortAscending = NULL;
    public $sortSourceByNull = NULL;
    public $sql=NULL;

    public function __construct($propertyName, $isSortAscending = TRUE,$sortSourceByNull=NULL) {
        parent::__construct();
        $this->propertyName = $propertyName;
        $this->isSortAscending = $isSortAscending;
        $this->sql = $sortSourceByNull;

        $this->checkPropertyName();
    }

    abstract protected function checkPropertyName();

    abstract public function formatPropertyNameAsDatabaseColumnName($maximumLength);

    public static function parseDirectionalPropertyName($directionalPropertyName,$sortSourceByNull=NULL) {
        $isSortAscending = TRUE;
        $propertyName = $directionalPropertyName;
        $sql = NULL;
        if ($directionalPropertyName[0] == self::$SORT_DIRECTION_DELIMITER__DESCENDING) {
            $isSortAscending = FALSE;
            $propertyName = substr($propertyName, 1);
            if (isset($sortSourceByNull)) {
                foreach ($sortSourceByNull as $value) {
                    if ($value == $propertyName) {
                        $sql = $propertyName . " " . "IS  NULL";
                    }
                }
            }
        }
        else if($directionalPropertyName[0] !== self::$SORT_DIRECTION_DELIMITER__DESCENDING) {
            if (isset($sortSourceByNull)) {
                foreach ($sortSourceByNull as $value) {
                    if ($value == $propertyName) {
                        $sql = $propertyName . " " . "IS NOT NULL";
                    }
                }
            }

        }

        //var_dump($sql);
        return array($propertyName, $isSortAscending,$sql);
    }

    public static function assembleDirectionalPropertyName($propertyName, $isSortAscending) {
        return ($isSortAscending ? '' : self::$SORT_DIRECTION_DELIMITER__DESCENDING) . $propertyName;
    }
}
