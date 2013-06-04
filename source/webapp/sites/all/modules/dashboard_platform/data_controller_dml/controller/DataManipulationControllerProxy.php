<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class DataManipulationControllerProxy extends AbstractDataControllerProxy {

    private static $factory = NULL;

    protected function prepareProxiedInstance() {
        return new DefaultDataManipulationController();
    }

    /**
     * @static
     * @return DataManipulationController
     */
    public static function getInstance() {
        if (!isset(self::$factory)) {
            self::$factory = new DataManipulationControllerProxy();
        }

        return self::$factory;
    }
}
