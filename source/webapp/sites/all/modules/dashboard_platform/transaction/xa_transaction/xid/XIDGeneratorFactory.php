<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


abstract class XIDGeneratorFactory extends AbstractFactory {

    private static $factory = NULL;

    /**
     * @static
     * @return XIDGeneratorFactory
     */
    public static function getInstance() {
        if (!isset(self::$factory)) {
            self::$factory = new DefaultXIDGeneratorFactory();
        }

        return self::$factory;
    }

    /**
     * @return XIDGenerator
     */
    abstract public function getGenerator();
}
