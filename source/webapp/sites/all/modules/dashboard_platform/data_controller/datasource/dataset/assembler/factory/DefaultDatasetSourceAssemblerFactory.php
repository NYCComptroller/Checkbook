<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




class DefaultDatasetSourceAssemblerFactory extends DatasetSourceAssemblerFactory {

    private $handlerConfigurations = NULL;

    public function __construct() {
        parent::__construct();
        $this->handlerConfigurations = module_invoke_all('dc_dataset_assembler');
    }

    protected function getHandlerConfiguration($type) {
        if (!isset($this->handlerConfigurations[$type])) {
            throw new IllegalArgumentException(t('Unsupported dataset assembler: @type', array('@type' => $type)));
        }

        return $this->handlerConfigurations[$type];
    }

    public function getHandler($assemblerType, $assemblerConfiguration) {
        $handlerConfiguration = $this->getHandlerConfiguration($assemblerType);
        $classname = $handlerConfiguration['classname'];

        return new $classname($assemblerConfiguration);
    }
}
