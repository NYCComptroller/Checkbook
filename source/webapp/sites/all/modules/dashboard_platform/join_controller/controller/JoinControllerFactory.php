<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




abstract class JoinControllerFactory extends AbstractFactory {

    private static $factory = NULL;

    /**
     * @static
     * @return JoinControllerFactory
     */
    public static function getInstance() {
        if (!isset(self::$factory)) {
            self::$factory = new DefaultJoinControllerFactory();
        }

        return self::$factory;
    }

    /**
     * @param $method
     * @return JoinController
     */
    abstract public function getHandler($method);

    abstract public function getSupportedMethods();
}
