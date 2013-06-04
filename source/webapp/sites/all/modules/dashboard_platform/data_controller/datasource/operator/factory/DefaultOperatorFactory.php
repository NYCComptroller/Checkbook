<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




class DefaultOperatorFactory extends OperatorFactory {

    private $handlerConfigurations = NULL;
    private $handlerMetaDataInstances = NULL;

    public function __construct() {
        parent::__construct();
        $this->handlerConfigurations = module_invoke_all('dc_datasource_operator');
    }

    public function initiateHandler($operatorName) {
        $values = func_get_args();
        array_shift($values);

        $handlerConfiguration = $this->getHandlerConfiguration($operatorName);

        $classname = $handlerConfiguration['handler']['classname'];

        $handlerClass = new ReflectionClass($classname);

        $params = NULL;
        // first parameter is the operator configuration
        $operatorMetaData = $this->getOperatorMetaData($operatorName);
        $params[] = $operatorMetaData;
        // next are parameters which represent values
        if ((count($values) === 1) && is_array($values[0])) {
            $parameterCount = count($values[0]);

            $expectedMinimumParameterCount = isset($operatorMetaData) ? $operatorMetaData->getRequiredParameterCount() : 0;
            $expectedTotalParameterCount = isset($operatorMetaData) ? $operatorMetaData->getParameterCount() : 0;

            if ($parameterCount == $expectedTotalParameterCount) {
                ArrayHelper::mergeArrays($params, $values[0]);
            }
            elseif ($expectedTotalParameterCount === 1) {
                $params[] = $values[0];
            }
            elseif (($parameterCount < $expectedTotalParameterCount) && ($parameterCount >= $expectedMinimumParameterCount)) {
                // we have some optional parameters which do not need to be provided
                ArrayHelper::mergeArrays($params, $values[0]);
            }
            else {
                throw new IllegalArgumentException(t("Inconsistent number of arguments for '@name' operator", array('@name' => $operatorName)));
            }
        }
        else {
            ArrayHelper::mergeArrays($params, $values);
        }

        return $handlerClass->newInstanceArgs($params);
    }

    protected function getHandlerConfiguration($operatorName) {
        if (!isset($this->handlerConfigurations[$operatorName])) {
            throw new IllegalArgumentException(t('Unsupported operator: @name', array('@name' => $operatorName)));
        }

        return $this->handlerConfigurations[$operatorName];
    }

    protected function registerOperatorMetaDataInstance($operatorName, $metadataInstance) {
        $this->handlerMetaDataInstances[$operatorName] = $metadataInstance;
    }

    public function getSupportedOperators() {
        $supportedOperators = NULL;

        foreach ($this->handlerConfigurations as $operatorName => $handlerConfiguration) {
            $supportedOperators[$operatorName] = $handlerConfiguration['description'];
        }

        return $supportedOperators;
    }

    public function isSupported($operatorName) {
        $supportedOperators = $this->getSupportedOperators();

        return isset($supportedOperators[$operatorName]);
    }

    public function getOperatorMetaData($operatorName) {
        if (isset($this->handlerMetaDataInstances[$operatorName])) {
            $metadataInstance = $this->handlerMetaDataInstances[$operatorName];
        }
        else {
            $handlerConfiguration = $this->getHandlerConfiguration($operatorName);

            $classname = isset($handlerConfiguration['metadata']['classname'])
                ? $handlerConfiguration['metadata']['classname']
                : NULL;

            $metadataInstance = isset($classname) ? new $classname() : FALSE;

            $this->registerOperatorMetaDataInstance($operatorName, $metadataInstance);
        }

        return ($metadataInstance === FALSE) ? NULL : $metadataInstance;
    }
}
