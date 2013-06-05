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


abstract class AbstractDataSourceHandler extends AbstractObject implements DataSourceHandler {

    private $datasourceType = NULL;
    private $extensions = NULL;
    private $extensionInstances = NULL;
    private $originalDataType4ReferencedDataTypes = NULL;

    public function __construct($datasourceType, $extensions) {
        parent::__construct();
        $this->datasourceType = $datasourceType;
        $this->extensions = $extensions;
    }

    public function getDataSourceType() {
        return $this->datasourceType;
    }

    public function getExtension($functionalityName) {
        if (isset($this->extensionInstances[$functionalityName])) {
            return $this->extensionInstances[$functionalityName];
        }

        $extensionClassName = isset($this->extensions[$functionalityName])
            ? $this->extensions[$functionalityName]
            : NULL;
        if (!isset($extensionClassName)) {
            throw new IllegalStateException(t(
                "'@functionalityName' function is not implemented for '@datasourceType' data source type",
                array('@datasourceType' => $this->getDataSourceType(), '@functionalityName' => $functionalityName)));
        }

        $extensionInstance = new $extensionClassName();

        $this->extensionInstances[$functionalityName] = $extensionInstance;

        return $extensionInstance;
    }

    public function getMaximumEntityNameLength() {
        return $this->getExtension('getMaximumEntityNameLength')->getLength($this);
    }

    public function concatenateValues(array $formattedValues) {
        return $this->getExtension('concatenateValues')->concatenate($this, $formattedValues);
    }

    protected function adjustReferencedDataType4Casting($datasetName, $columnName) {
        throw new UnsupportedOperationException();
    }

    protected function prepareDataType4Casting($datatype) {
        if (isset($datatype)) {
            list($datasetName, $columnName) = ReferencePathHelper::splitReference($datatype);
            if (isset($datasetName)) {
                $adjustedDataType = isset($this->originalDataType4ReferencedDataTypes[$datatype])
                    ? $this->originalDataType4ReferencedDataTypes[$datatype]
                    : NULL;

                if (!isset($adjustedDataType)) {
                    $adjustedDataType = $this->adjustReferencedDataType4Casting($datasetName, $columnName);
                    $this->originalDataType4ReferencedDataTypes[$datatype] = $adjustedDataType;
                }

                return $adjustedDataType;
            }
        }

        return $datatype;
    }

    public function castValue($datatype, $value) {
        $datatypeHandler = isset($datatype) ? DataTypeFactory::getInstance()->getHandler($datatype) : NULL;

        // datatype-specific value casting
        $castValue = isset($datatypeHandler) ? $datatypeHandler->castValue($value) : NULL;
        if (isset($castValue)) {
            // database-specific value adjustment
            switch ($datatypeHandler->getStorageDataType()) {
                case DateDataTypeHandler::$DATA_TYPE:
                case TimeDataTypeHandler::$DATA_TYPE:
                case DateTimeDataTypeHandler::$DATA_TYPE:
                    // converting date value to storage format. All extensions should expect date value in that format
                    $dt = DateTime::createFromFormat($datatypeHandler->getMask(), $castValue);
                    $castValue = $dt->format($datatypeHandler->getStorageMask());
                    break;
                case BooleanDataTypeHandler::$DATA_TYPE:
                    $castValue = $castValue
                        ? BooleanDataTypeHandler::$STORAGE_VALUE__TRUE
                        : BooleanDataTypeHandler::$STORAGE_VALUE__FALSE;
                    break;
            }
        }

        return $castValue;
    }

    public function formatStringValue($value) {
        throw new UnsupportedOperationException();
    }

    public function formatDateValue($formattedValue, $mask) {
        return $this->getExtension('formatDateValue')->format($this, $formattedValue, $mask);
    }

    public function formatValue($datatype, $value) {
        $adjustedDataType = $this->prepareDataType4Casting($datatype);

        $castValue = $this->castValue($adjustedDataType, $value);
        if (isset($castValue)) {
            $datatypeHandler = DataTypeFactory::getInstance()->getHandler($adjustedDataType);

            // database-specific data adjustment
            switch ($datatypeHandler->getStorageDataType()) {
                case StringDataTypeHandler::$DATA_TYPE:
                    $formattedValue = $this->formatStringValue($castValue);
                    break;
                case IntegerDataTypeHandler::$DATA_TYPE:
                case NumberDataTypeHandler::$DATA_TYPE:
                case CurrencyDataTypeHandler::$DATA_TYPE:
                case PercentDataTypeHandler::$DATA_TYPE:
                    $formattedValue = $castValue;
                    break;
                case DateDataTypeHandler::$DATA_TYPE:
                case TimeDataTypeHandler::$DATA_TYPE:
                case DateTimeDataTypeHandler::$DATA_TYPE:
                    $formattedValue = $this->formatStringValue($castValue);
                    $formattedValue = $this->formatDateValue($formattedValue, $datatypeHandler->getStorageMask());
                    break;
                case BooleanDataTypeHandler::$DATA_TYPE:
                    $formattedValue = is_int($castValue) ? $castValue : $this->formatStringValue($castValue);
                    break;
                default:
                    throw new UnsupportedOperationException(t(
                        "Unsupported data type '@datatype' to format the value: @value",
                        array('@datatype' => $adjustedDataType, '@value' => $value)));
            }
        }
        else {
            $formattedValue = 'NULL';
        }

        return $formattedValue;
    }

    public function formatOperatorValue(DataControllerCallContext $callcontext, AbstractRequest $request, $datasetName, $columnName, OperatorHandler $value) {
        throw new UnsupportedOperationException();
    }

    public function startTransaction($datasourceName) {
        $this->errorTransactionNotSupported($datasourceName);
    }

    public function commitTransaction($datasourceName) {
        $this->errorTransactionNotSupported($datasourceName);
    }

    public function rollbackTransaction($datasourceName) {
        $this->errorTransactionNotSupported($datasourceName);
    }

    protected function errorTransactionNotSupported($datasourceName) {
        $environment_metamodel = data_controller_get_environment_metamodel();

        $datasource = $environment_metamodel->getDataSource($datasourceName);

        throw new UnsupportedOperationException(t(
            'Transaction support is not available for the data source: @datasourceName',
            array('@datasourceName' => $datasource->publicName)));
    }
}
