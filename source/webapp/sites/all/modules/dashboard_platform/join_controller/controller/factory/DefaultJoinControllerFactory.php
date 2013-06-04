<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class DefaultJoinControllerFactory extends JoinControllerFactory {

    private $handlerConfigurations = NULL;
    private $handlerInstances = NULL;

    protected function __construct() {
        parent::__construct();
        $this->handlerConfigurations = module_invoke_all('jc_method');
    }

    protected function getHandlerConfiguration($method) {
        if (isset($this->handlerConfigurations[$method])) {
            return $this->handlerConfigurations[$method];
        }

        throw new IllegalArgumentException(t('Unsupported join method: @method', array('@method' => $method)));
    }

    public function getHandler($method) {
        if (isset($this->handlerInstances[$method])) {
            return $this->handlerInstances[$method];
        }

        $handlerConfiguration = $this->getHandlerConfiguration($method);
        $classname = $handlerConfiguration['classname'];

        $handler = new $classname();

        $this->handlerInstances[$method] = $handler;

        return $handler;
    }

    public function getSupportedMethods() {
        return array_keys($this->handlerConfigurations);
    }
}
