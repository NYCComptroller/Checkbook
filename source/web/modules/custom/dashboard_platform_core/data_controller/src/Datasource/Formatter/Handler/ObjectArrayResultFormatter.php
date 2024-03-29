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

namespace Drupal\data_controller\Datasource\Formatter\Handler;

use stdClass;


class ObjectArrayResultFormatter extends AbstractResultFormatter {

    public function formatRecord(array &$records = NULL, $record) {
        $result = parent::formatRecord($records, $record);
        if ($result) {
            $this->errorUnsupportedChainOfResultFormatters();
        }

        $object = new stdClass();
        foreach ($record as $columnName => $columnValue) {
            $index = strpos($columnName, '.');
            if ($index === FALSE) {
                $object->$columnName = $columnValue;
            }
            else {
                $properties = explode('.', $columnName);

                $obj = $object;
                for ($i = 0, $count = count($properties); $i < $count; $i++) {
                    $property = $properties[$i];
                    if ($i == ($count - 1)) {
                        $obj->$property = $columnValue;
                    }
                    else {
                        if (!isset($obj->$property)) {
                            $obj->$property = new stdClass();
                        }
                        $obj = $obj->$property;
                    }
                }
            }
        }

        $records[] = $object;

        return TRUE;
    }
}
