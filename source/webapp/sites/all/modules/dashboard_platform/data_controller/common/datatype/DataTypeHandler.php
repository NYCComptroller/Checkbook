<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
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
