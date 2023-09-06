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

use Drupal\data_controller\Common\Datatype\DataTypeFactory;
use Drupal\data_controller\Common\Object\Exception\IllegalArgumentException;


class IntegerDataTypeHandler extends AbstractIntegerDataTypeHandler {

    public static $DATA_TYPE = 'integer';

    public static function checkNonNegativeInteger($value) {
        if (!isset($value)) {
            return;
        }

        DataTypeFactory::getInstance()->checkValueType(self::$DATA_TYPE, $value);

        if ($value < 0) {
            //LogHelper::log_error(t("'@value' is a negative integer", array('@value' => $value)));
            throw new IllegalArgumentException(t('Value is a negative integer'));
        }
    }

    public function getStorageDataType() {
        return self::$DATA_TYPE;
    }

    public function selectCompatible($datatype) {
        return ($datatype == NumberDataTypeHandler::$DATA_TYPE)
            ? $datatype
            : parent::selectCompatible($datatype);
    }
}
