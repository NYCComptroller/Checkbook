<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


abstract class AbstractDataControllerProxy extends AbstractFactory {

    protected $instance = NULL;

    protected function __construct() {
        parent::__construct();

        $this->instance = $this->prepareProxiedInstance();
    }

    abstract protected function prepareProxiedInstance();

    public function __call($methodName, $args) {
        $timeStart = microtime(TRUE);
        $result = call_user_func_array(array($this->instance, $methodName), $args);
        LogHelper::log_info(t(
            'Data Controller execution time for @methodName(): !executionTime',
            array('@methodName' => $methodName, '!executionTime' => ExecutionPerformanceHelper::formatExecutionTime($timeStart))));

        return $result;
    }
}
