<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


abstract class DimensionLookupFactory extends AbstractFactory {

    private static $factory = NULL;

    /**
     * @static
     * @return DimensionLookupFactory
     */
    public static function getInstance() {
        if (!isset(self::$factory)) {
            self::$factory = new DefaultDimensionLookupFactory();
        }

        return self::$factory;
    }

    abstract function registerHandlerConfiguration($datatype, $classname);

    /**
     * @param $datatype
     * @return DimensionLookupHandler
     */
    abstract public function findHandler($datatype);

    /**
     * @param $datatype
     * @return DimensionLookupHandler
     */
    abstract public function getHandler($datatype);
}
