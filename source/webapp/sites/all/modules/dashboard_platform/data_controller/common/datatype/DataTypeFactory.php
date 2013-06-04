<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


abstract class DataTypeFactory extends AbstractFactory {

    private static $factory = NULL;

    /**
     * @static
     * @return DataTypeFactory
     */
    public static function getInstance() {
        if (!isset(self::$factory)) {
            self::$factory = new DefaultDataTypeFactory();
        }

        return self::$factory;
    }

    abstract public function getSupportedDataTypes();

    abstract public function autoDetectDataType($value);
    abstract public function autoDetectPrimaryDataType(array $values = NULL);
    abstract public function selectDataType(array $datatypes, $selectCompatible = TRUE);
    abstract public function checkValueType($datatype, $value);

    /**
     * @param $datatype
     * @return DataTypeHandler
     */
    abstract public function getHandler($datatype);
}
