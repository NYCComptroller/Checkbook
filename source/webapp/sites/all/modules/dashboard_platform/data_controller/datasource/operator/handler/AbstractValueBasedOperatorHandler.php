<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




abstract class AbstractValueBasedOperatorHandler extends AbstractSingleParameterBasedOperatorHandler {

    public function __construct($configuration, $value = NULL) {
        parent::__construct($configuration);

        $adjustedValue = is_array($value)
            ? ArrayElementTrimmer::trimList($value)
            : StringHelper::trim($value);
        if (is_array($adjustedValue) && count($adjustedValue) === 1) {
            $adjustedValue = $adjustedValue[0];
        }

        $parameterName = $this->getParameterName();
        $this->$parameterName = $adjustedValue;
    }

    public function getParameterDataType() {
        $parameterName = $this->getParameterName();
        $value = $this->$parameterName;

        return is_array($value)
            ? DataTypeFactory::getInstance()->autoDetectPrimaryDataType($value)
            : DataTypeFactory::getInstance()->autoDetectDataType($value);
    }
}

class ValueBasedOperatorHandler extends AbstractValueBasedOperatorHandler {

    protected function getParameterName() {
        return 'value';
    }
}

abstract class AbstractSingleValueBasedOperatorHandler extends AbstractSingleParameterBasedOperatorHandler {

    public function __construct($configuration, $value = NULL) {
        parent::__construct($configuration);

        if (is_array($value)) {
            $values = (array) $value;
            throw new IllegalArgumentException(t(
            	'Only single value is supported for the operator: [@value]',
                array('@value' => implode(', ', $values))));
        }

        $parameterName = $this->getParameterName();
        $this->$parameterName = StringHelper::trim($value);
    }

    public function getParameterDataType() {
        $parameterName = $this->getParameterName();
        $value = $this->$parameterName;

        return DataTypeFactory::getInstance()->autoDetectDataType($value);
    }
}

class SingleValueBasedOperatorHandler extends AbstractSingleValueBasedOperatorHandler {

    protected function getParameterName() {
        return 'value';
    }
}

class ValueBasedOperatorMetaData extends AbstractOperatorMetaData {

    protected function initiateParameters() {
        return array(new OperatorParameter('value', 'Value'));
    }
}
