<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




class DataQueryControllerProxy extends AbstractDataControllerProxy {

    private static $factory = NULL;

    protected function prepareProxiedInstance() {
        return new DefaultDataQueryController();
    }

    /**
     * @static
     * @return DataQueryController
     */
    public static function getInstance() {
        if (!isset(self::$factory)) {
            self::$factory = new DataQueryControllerProxy();
        }

        return self::$factory;
    }
}
