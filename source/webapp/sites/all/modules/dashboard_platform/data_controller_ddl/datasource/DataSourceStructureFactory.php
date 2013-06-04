<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class DataSourceStructureFactory extends AbstractDataSourceStructureFactory {

    private static $factory = NULL;

    /**
     * @static
     * @return DataSourceStructureFactory
     */
    public static function getInstance() {
        if (!isset(self::$factory)) {
            self::$factory = new DataSourceStructureFactory();
        }

        return self::$factory;
    }

    /**
     * @param string $type
     * @return DataSourceStructureHandler
     */
    public function getHandler($type) {
        return parent::getHandler($type);
    }
}
