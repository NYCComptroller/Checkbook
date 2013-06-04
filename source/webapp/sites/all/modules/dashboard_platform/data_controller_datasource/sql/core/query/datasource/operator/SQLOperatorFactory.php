<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




abstract class SQLOperatorFactory extends AbstractFactory {

    private static $factory = NULL;

    /**
     * @static
     * @return SQLOperatorFactory
     */
    public static function getInstance() {
        if (!isset(self::$factory)) {
            self::$factory = new DefaultSQLOperatorFactory();
        }

        return self::$factory;
    }

    /**
     * @abstract
     * @param DataSourceQueryHandler $datasourceQueryHandler
     * @param OperatorHandler $operatorHandler
     * @return SQL_AbstractOperatorHandler
     */
    public abstract function getHandler(DataSourceQueryHandler $datasourceQueryHandler, OperatorHandler $operatorHandler);
}
