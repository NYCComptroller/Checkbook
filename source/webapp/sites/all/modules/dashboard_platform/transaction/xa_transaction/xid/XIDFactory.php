<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


abstract class XIDFactory extends AbstractFactory {

    private static $factory = NULL;

    /**
     * @static
     * @return XIDFactory
     */
    public static function getInstance() {
        if (!isset(self::$factory)) {
            self::$factory = new DefaultXIDFactory();
        }

        return self::$factory;
    }

    /**
     * @return XID
     */
    abstract public function newXID();
}
