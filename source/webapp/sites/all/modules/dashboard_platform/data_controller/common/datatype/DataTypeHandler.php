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


define('DATA_TYPE__PRIMITIVE', 0x0001);
define('DATA_TYPE__BUSINESS', 0x0002);
define('DATA_TYPE__ALL', 0xFFFF);

interface DataTypeHandler {

    // use one of DATA_TYPE_* to define type of the handler
    function getHandlerType();

    function getMask();
    function getStorageMask();

    /*
     * Returns primitive data type which could be used to convert value of this data type to storage format
     */
    function getStorageDataType();

    /*
     * Checks if the value is of this data type natively
     */
    function isValueOf($value);
    /*
     * Selects the best data type to cast to
     */
    function selectCompatible($datatype);
    /*
     * Checks if the value can be parsed to this data type
     */
    function isParsable($value);
    /*
     * Force cast of the value to this data type
     */
    function castValue($value);
}
