<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




abstract class DatasetSourceAssemblerFactory extends AbstractFactory {

    private static $factory = NULL;

    public static function getInstance() {
        if (!isset(self::$factory)) {
            self::$factory = new DefaultDatasetSourceAssemblerFactory();
        }

        return self::$factory;
    }

    /**
     * @param $assemblerType
     * @param $assemblerConfiguration
     * @return DatasetSourceAssembler
     */
    abstract public function getHandler($assemblerType, $assemblerConfiguration);
}
