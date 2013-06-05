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
