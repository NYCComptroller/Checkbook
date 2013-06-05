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


class MySQLFormatDateValueImpl extends AbstractFormatDateValueImpl {

    protected function adjustMask($mask) {
        $adjustedMask = '';

        for ($i = 0, $len = strlen($mask); $i < $len; $i++) {
            $specifier = $mask[$i];

            $mysqlSpecifier = $specifier;
            switch ($specifier) {
                case 'Y':
                case 'm':
                case 'd':
                case 'H':
                case 'i':
                case 's':
                    $mysqlSpecifier = '%' . $specifier;
                    break;
            }

            $adjustedMask .= $mysqlSpecifier;
        }

        return $adjustedMask;
    }

    public function formatImpl(DataSourceHandler $handler, $formattedValue, $adjustedMask) {
        return "STR_TO_DATE($formattedValue, '$adjustedMask')";
    }
}
