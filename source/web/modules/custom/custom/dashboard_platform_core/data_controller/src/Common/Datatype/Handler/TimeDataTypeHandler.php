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
