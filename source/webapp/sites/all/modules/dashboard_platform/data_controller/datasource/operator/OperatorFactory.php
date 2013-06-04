<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




abstract class OperatorFactory extends AbstractFactory {

    private static $factory = NULL;

    /**
     * @static
     * @return OperatorFactory
     */
    public static function getInstance() {
        if (!isset(self::$factory)) {
            self::$factory = new DefaultOperatorFactory();
        }

        return self::$factory;
    }

    /**
     * @param $operatorName
     *     A string containing name of an operator.
     * @param ...
     *     A variable number of arguments which are passed to corresponding operator handler instance.
     *     Instead of a variable number of arguments, you may also pass a single array containing the arguments.
     */
    public abstract function initiateHandler($operatorName);
    public abstract function isSupported($operatorName);
    public abstract function getSupportedOperators();

    /**
     * @param $operatorName
     * @return AbstractOperatorMetaData
     */
    public abstract function getOperatorMetaData($operatorName);
}
