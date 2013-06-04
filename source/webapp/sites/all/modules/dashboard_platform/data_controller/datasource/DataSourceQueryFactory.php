<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




class DataSourceQueryFactory extends AbstractDataSourceQueryFactory {

    private static $factory = NULL;

    /**
     * @static
     * @return DataSourceQueryFactory
     */
    public static function getInstance() {
        if (!isset(self::$factory)) {
            self::$factory = new DataSourceQueryFactory();
        }

        return self::$factory;
    }

    /**
     * @param string $type
     * @return DataSourceQueryHandler
     */
    public function getHandler($type) {
        return parent::getHandler($type);
    }
}
