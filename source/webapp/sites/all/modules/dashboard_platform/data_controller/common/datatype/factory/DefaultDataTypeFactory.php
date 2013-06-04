<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class IncompatibleDataTypeException extends IllegalStateException {}

class DefaultDataTypeFactory extends DataTypeFactory {

    private $handlerConfigurations = NULL;
    private $handlerInstances = NULL;

    public function __construct() {
        parent::__construct();
        $this->handlerConfigurations = module_invoke_all('dc_data_type');
    }

    protected function getHandlerConfiguration($datatype) {
        if (isset($this->handlerConfigurations[$datatype])) {
            return $this->handlerConfigurations[$datatype];
        }

        throw new IllegalArgumentException(t('Unsupported data type: @datatype', array('@datatype' => $datatype)));
    }

    public function getSupportedDataTypes() {
        $supportedDataTypes = NULL;

        foreach ($this->handlerConfigurations as $datatype => $handlerConfiguration) {
            $supportedDataTypes[$datatype] = $handlerConfiguration['description'];
        }

        return $supportedDataTypes;
    }

    public function autoDetectDataType($value, $handlerType = DATA_TYPE__PRIMITIVE) {
        if (!isset($value)) {
            return NULL;
        }

        $adjustedValue = $value;
        if (is_string($adjustedValue)) {
            $stringHandler = $this->getHandler(StringDataTypeHandler::$DATA_TYPE);
            $adjustedValue = $stringHandler->castValue($adjustedValue);
            if (!isset($adjustedValue)) {
                return NULL;
            }
        }

        // checking if the value of predefined type already
        $compatibleDataTypesBasedOnValue = NULL;
        foreach ($this->handlerConfigurations as $datatype => $configuration) {
            $handler = $this->getHandler($datatype);
            if (($handler->getHandlerType() & $handlerType) === 0) {
                continue;
            }

            if ($handler->isValueOf($adjustedValue)) {
                $compatibleDataTypesBasedOnValue[] = $datatype;
            }
        }
        if (isset($compatibleDataTypesBasedOnValue)) {
            $compatibleDataTypeBasedOnValue = $this->selectDataType($compatibleDataTypesBasedOnValue, FALSE);
            if ($compatibleDataTypeBasedOnValue == StringDataTypeHandler::$DATA_TYPE) {
                // further detection is possible
            }
            else {
                return $compatibleDataTypeBasedOnValue;
            }
        }
        else {
            throw new IllegalArgumentException(t('Could not detect type of the value: @value', array('@value' => $value)));
        }

        // checking if the string value can be parsed to some other type
        $compatibleDataTypesBasedOnParsedValue = NULL;
        foreach ($this->handlerConfigurations as $datatype => $configuration) {
            // ignoring string type
            if ($datatype === StringDataTypeHandler::$DATA_TYPE) {
                continue;
            }

            $handler = $this->getHandler($datatype);
            if (($handler->getHandlerType() & $handlerType) === 0) {
                continue;
            }

            if ($handler->isParsable($adjustedValue)) {
                $compatibleDataTypesBasedOnParsedValue[] = $datatype;
            }
        }
        // there is no other types which could parse the value
        if (!isset($compatibleDataTypesBasedOnParsedValue)) {
            return StringDataTypeHandler::$DATA_TYPE;
        }

        return $this->selectDataType($compatibleDataTypesBasedOnParsedValue, FALSE);
    }

    public function autoDetectPrimaryDataType(array $values = NULL, $handlerType = DATA_TYPE__PRIMITIVE) {
        $primaryDataType = NULL;

        if (isset($values)) {
            $compatibleDataTypes = NULL;
            foreach ($values as $value) {
                $valueDateType = $this->autoDetectDataType($value, $handlerType);
                ArrayHelper::addUniqueValue($compatibleDataTypes, $valueDateType);
            }

            $primaryDataType = $this->selectDataType($compatibleDataTypes);
        }

        return $primaryDataType;
    }

    public function selectDataType(array $datatypes, $selectCompatible = TRUE) {
        // eliminating null records from the array
        if (isset($datatypes)) {
            foreach ($datatypes as $index => $datatype) {
                if (!isset($datatype)) {
                    unset($datatypes[$index]);
                }
            }
            if (count($datatypes) == 0) {
                $datatypes = NULL;
            }
        }
        if (!isset($datatypes)) {
            return NULL;
        }

        // checking if all elements are equal
        $selectedDataType = NULL;
        foreach ($datatypes as $datatype) {
            if (isset($selectedDataType)) {
                if ($selectedDataType != $datatype) {
                    $selectedDataType = NULL;
                    break;
                }
            }
            else {
                $selectedDataType = $datatype;
            }
        }

        if (isset($selectedDataType)) {
            return $selectedDataType;
        }

        // checking if we need to return compatible type
        if (!$selectCompatible) {
            // if some types are not compatible we need to return the lowest compatible type
            if (!$this->areCompatible($datatypes)) {
                $selectCompatible = TRUE;
            }
        }

        $selectedDataTypes = $datatypes;
        if ($selectCompatible) {
            do {
                $initialDataTypeCount = count($selectedDataTypes);
                $selectedDataTypes = $this->selectCompatible($selectedDataTypes);
                $selectedDataTypeCount = count($selectedDataTypes);
                if (($initialDataTypeCount > 1) && ($initialDataTypeCount == $selectedDataTypeCount)) {
                    throw new IncompatibleDataTypeException(t(
                    	'Data types are inter-compatible. Single type could not be selected: @datatypes',
                        array('@datatypes' => ArrayHelper::printArray($datatypes, ',', TRUE, FALSE))));
                }
            }
            while ($selectedDataTypeCount > 1);
        }
        else {
            // it is expected that we have only 2 types here
            while (($datatypeCount = count($selectedDataTypes)) >= 2) {
                $datatypeA = $selectedDataTypes[0];
                $datatypeB = $selectedDataTypes[1];
                $selectedByDataTypeA = $this->getHandler($datatypeA)->selectCompatible($datatypeB);
                $selectedByDataTypeB = $this->getHandler($datatypeB)->selectCompatible($datatypeA);

                $selectedDataType = NULL;
                if (isset($selectedByDataTypeA)) {
                    if (isset($selectedByDataTypeB)) {
                        throw new IncompatibleDataTypeException(t(
                        	"Data types '@datatypeA' and '@datatypeB' are inter-compatible. Single type could not be selected",
                            array('@datatypeA' => $datatypeA, '@datatypeB' => $datatypeB)));
                    }
                    else {
                        $selectedDataType = $selectedByDataTypeA;
                    }
                }
                elseif (isset($selectedByDataTypeB)) {
                    $selectedDataType = $selectedByDataTypeB;
                }
                else {
                    throw new UnsupportedOperationException();
                }

                $selectedDataTypes = array_slice($selectedDataTypes, 2);
                // selecting opposite data type. We do not need the lowest compatible type
                $selectedDataTypes[] = ($selectedDataType == $datatypeA) ? $datatypeB : $datatypeA;
            }
        }

        if (count($selectedDataTypes) == 1) {
            return $selectedDataTypes[0];
        }
        else {
            throw new IncompatibleDataTypeException(t(
            	'Incompatible data types: @datatypes',
                array('@datatypes' => ArrayHelper::printArray($datatypes, ',', TRUE, FALSE))));
        }
    }

    protected function areCompatible(array $datatypes) {
        for ($i = 0, $count = count($datatypes) - 1; $i < $count; $i++) {
            $datatypeA = $datatypes[$i];
            $datatypeB = $datatypes[$i + 1];

            $selectedByDataTypeA = $this->getHandler($datatypeA)->selectCompatible($datatypeB);
            $selectedByDataTypeB = $this->getHandler($datatypeB)->selectCompatible($datatypeA);

            if (!isset($selectedByDataTypeA) && !isset($selectedByDataTypeB)) {
                return FALSE;
            }

            if (!$this->areCompatible(array_slice($datatypes, 1))) {
                return FALSE;
            }
        }

        return TRUE;
    }

    protected function selectCompatible(array $datatypes) {
        $compatibleDataTypes = NULL;

        for ($i = 0, $count = count($datatypes) - 1; $i < $count; $i++) {
            $datatypeA = $datatypes[$i];
            $datatypeB = $datatypes[$i + 1];

            $selectedByDataTypeA = $this->getHandler($datatypeA)->selectCompatible($datatypeB);
            $selectedByDataTypeB = $this->getHandler($datatypeB)->selectCompatible($datatypeA);

            ArrayHelper::addUniqueValues($compatibleDataTypes, array($selectedByDataTypeA, $selectedByDataTypeB));

            ArrayHelper::addUniqueValues($compatibleDataTypes, $this->selectCompatible(array_slice($datatypes, 1)));
        }

        return $compatibleDataTypes;
    }

    public function checkValueType($datatype, $value) {
        $handler = $this->getHandler($datatype);
        $allowedHandlerType = DATA_TYPE__PRIMITIVE | $handler->getHandlerType();

        $detectedDataType = $this->autoDetectDataType($value, $allowedHandlerType);
        // checking if there is exact match
        if (isset($detectedDataType) && ($datatype != $detectedDataType)) {
            // checking if the types are compatible
            $selectedByDataType = $handler->selectCompatible($detectedDataType);
            $selectedByDetectedDataType = $this->getHandler($detectedDataType)->selectCompatible($datatype);
            if (isset($selectedByDataType) || isset($selectedByDetectedDataType)) {
                // the types are compatible
            }
            else {
                LogHelper::log_error(t(
                    "'@value' is of type '@detectedDataType'. Requested type is '@requestedDataType'",
                    array('@requestedDataType' => $datatype, '@detectedDataType' => $detectedDataType, '@value' => $value)));
                throw new IllegalArgumentException(t("Value is not of type '@datatype'", array('@datatype' => $datatype)));
            }
        }
    }

    /**
     * @param $datatype
     * @return DataTypeHandler
     */
    public function getHandler($datatype) {
        if (isset($this->handlerInstances[$datatype])) {
            return $this->handlerInstances[$datatype];
        }

        $handlerConfiguration = $this->getHandlerConfiguration($datatype);
        $classname = $handlerConfiguration['classname'];

        $handler = new $classname();

        // storing the instance in the internal cache
        $this->handlerInstances[$datatype] = $handler;

        return $handler;
    }
}
