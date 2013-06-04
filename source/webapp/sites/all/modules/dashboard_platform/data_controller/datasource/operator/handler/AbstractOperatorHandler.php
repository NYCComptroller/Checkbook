<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




abstract class AbstractOperatorHandler extends AbstractObject implements OperatorHandler {

    public $metadata = NULL;
    public $weight = NULL;

    protected $calculatedValues = NULL;
    protected $calculatedValueFlags = NULL; // we need the flags because calculated values could be NULL

    public function __construct($metadata) {
        parent::__construct();
        $this->metadata = $metadata;
    }

    public function __clone() {
        parent::__clone();

        // when we clone this object we need to clean precalculated values
        // usually clonning is done before  modification of this or parent objects and that could lead to different calculation
        $this->calculatedValues = NULL;
        $this->calculatedValueFlags = NULL;
    }

    public function isSubsetBased() {
        return FALSE;
    }

    public function wasValueCalculated($variableName) {
        return isset($this->calculatedValueFlags[$variableName]);
    }

    public function getCalculatedValue($variableName) {
        if (!$this->wasValueCalculated($variableName)) {
            throw new IllegalArgumentException("'@variableName' variable has not been calculated", array('@variableName' => $variableName));
        }

        return isset($this->calculatedValues[$variableName]) ? $this->calculatedValues[$variableName] : NULL;
    }

    public function setCalculatedValue($variableName, $value) {
        $this->calculatedValues[$variableName] = $value;
        $this->calculatedValueFlags[$variableName] = TRUE;
    }
}

abstract class AbstractSingleParameterBasedOperatorHandler extends AbstractOperatorHandler implements ParameterBasedOperatorHandler {

    abstract protected function getParameterName();
}

abstract class AbstractOperatorMetaData extends AbstractObject implements OperatorMetaData {

    protected $parameters = NULL;

    public function __construct() {
        parent::__construct();
        $parameters = $this->initiateParameters();
        $this->checkParameters($parameters);

        $this->parameters = $parameters;
    }

    abstract protected function initiateParameters();

    protected function checkParameters(array $parameters = NULL) {
        if (!isset($parameters)) {
            return;
        }

        // optional parameters have to be at the end of the list
        $optionalParameterFound = FALSE;
        foreach ($parameters as $parameter) {
            if ($parameter->required) {
                if ($optionalParameterFound) {
                    throw new IllegalStateException(t(
                        "Optional parameter '@parameterName' cannot be placed before any required parameters",
                        array('@parameterName' => $parameter->name)));
                }
            }
            else {
                $optionalParameterFound = TRUE;
            }
        }
    }

    /**
     * @return OperatorParameter[]
     */
    public function getParameters() {
        return $this->parameters;
    }

    public function getParameterCount() {
        return count($this->parameters);
    }

    public function getRequiredParameterCount() {
        $requiredParameterCount = 0;

        if (isset($this->parameters)) {
            foreach ($this->parameters as $parameter) {
                if ($parameter->required) {
                    $requiredParameterCount++;
                }
            }
        }

        return $requiredParameterCount;
    }

    public function checkParameterName($parameterName) {
        $supportedParameters = NULL;

        if (isset($this->parameters)) {
            foreach ($this->parameters as $parameter) {
                if ($parameter->name === $parameterName) {
                    return;
                }

                $supportedParameters[] = $parameter->name;
            }
        }

        throw new IllegalArgumentException(t(
        	"'@parameterName' parameter is not supported by the operator.@supportedParametersIfAny",
            array(
            	'@parameterName' => $parameterName,
            	'@supportedParametersIfAny' =>
                    (isset($supportedParameters)
                        ? t(' Supported parameters: [@supportedParameters]', array('@supportedParameters' => implode(', ', $supportedParameters)))
                        : t(' No parameters are supported')))));
    }
}
