<?php
/**
* This file is part of the Checkbook NYC financial transparency software.
* 
* Copyright (C) 2012, 2013 New York City
* 
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU Affero General Public License as
* published by the Free Software Foundation, either version 3 of the
* License, or (at your option) any later version.
* 
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU Affero General Public License for more details.
* 
* You should have received a copy of the GNU Affero General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/




class DefaultOperatorFactory extends OperatorFactory {

    /**
     * @var array|null
     */
    private $handlerConfigurations = NULL;
    /**
     * @var null
     */
    private $handlerMetaDataInstances = NULL;

    /**
     * DefaultOperatorFactory constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->handlerConfigurations = module_invoke_all('dc_datasource_operator');
    }

    /**
     * @param mixed ...$values
     * @return mixed|object
     * @throws IllegalArgumentException
     * @throws ReflectionException
     */
    public function initiateHandler(...$values) {
        $operatorName = array_shift($values);

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

    /**
     * @param $operatorName
     * @return mixed
     * @throws IllegalArgumentException
     */
    protected function getHandlerConfiguration($operatorName) {
        if (!isset($this->handlerConfigurations[$operatorName])) {
            throw new IllegalArgumentException(t('Unsupported operator: @name', array('@name' => $operatorName)));
        }

        return $this->handlerConfigurations[$operatorName];
    }

    /**
     * @param $operatorName
     * @param $metadataInstance
     */
    protected function registerOperatorMetaDataInstance($operatorName, $metadataInstance) {
        $this->handlerMetaDataInstances[$operatorName] = $metadataInstance;
    }

    /**
     * @return mixed|null
     */
    public function getSupportedOperators() {
        $supportedOperators = NULL;

        foreach ($this->handlerConfigurations as $operatorName => $handlerConfiguration) {
            $supportedOperators[$operatorName] = $handlerConfiguration['description'];
        }

        return $supportedOperators;
    }

    /**
     * @param $operatorName
     * @return bool|mixed
     */
    public function isSupported($operatorName) {
        $supportedOperators = $this->getSupportedOperators();

        return isset($supportedOperators[$operatorName]);
    }

    /**
     * @param $operatorName
     * @return AbstractOperatorMetaData|bool|null
     * @throws IllegalArgumentException
     */
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
