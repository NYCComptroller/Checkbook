<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


abstract class AbstractDataSourceFactory extends AbstractFactory {

    private $handlerConfigurations = NULL;
    private $extensionConfigurations = NULL;
    private $handlerInstances = NULL;

    protected function __construct() {
        parent::__construct();

        $this->extensionConfigurations = module_invoke_all('dc_datasource');
        $this->handlerConfigurations = module_invoke_all($this->getHookName());
    }

    abstract protected function getFactoryPublicNamePrefix();

    abstract protected function getHookName();

    protected function getExtensionConfiguration($type) {
        return isset($this->extensionConfigurations[$type]) ? $this->extensionConfigurations[$type] : NULL;
    }

    protected function getHandlerConfiguration($type) {
        if (!isset($this->handlerConfigurations[$type])) {
            $prefix = $this->getFactoryPublicNamePrefix();
            throw new IllegalArgumentException(t("Unsupported $prefix Data Source handler: @type", array('@type' => $type)));
        }

        return $this->handlerConfigurations[$type];
    }

    public function getHandler($type) {
        if (isset($this->handlerInstances[$type])) {
            return $this->handlerInstances[$type];
        }

        $handlerConfiguration = $this->getHandlerConfiguration($type);
        $extensionConfiguration = $this->getExtensionConfiguration($type);

        $combinedExtensionConfigurations = NULL;
        // adding generic configuration
        ArrayHelper::mergeArrays($combinedExtensionConfigurations, $extensionConfiguration['extensions']);
        // adding handler specific configurations
        if (isset($handlerConfiguration['extensions'])) {
            ArrayHelper::mergeArrays($combinedExtensionConfigurations, $handlerConfiguration['extensions']);
            unset($handlerConfiguration['extensions']);
        }

        $classname = $handlerConfiguration['handler'];

        $handler = new $classname($type, $combinedExtensionConfigurations);

        $this->handlerInstances[$type] = $handler;

        return $handler;
    }
}
