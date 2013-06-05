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


abstract class AbstractCreateTableStatementImpl extends AbstractObject {

    public function generate(DataSourceHandler $handler, DatasetMetaData $dataset) {
        return $this->prepareCreateTableStatement($handler, $dataset);
    }

    // *****************************************************************************************************************************
    //      Type: String
    // *****************************************************************************************************************************
    protected function maxLength4VariableLengthString() {
        return 255;
    }

    protected function assembleVariableLengthString(ColumnMetaData $column) {
        $column->type->databaseType = 'VARCHAR' . '(' . $column->type->length . ')';
    }

    abstract protected function assembleLongString(ColumnMetaData $column);

    protected function prepareVariableLengthString(ColumnMetaData $column, $length = NULL) {
        $overhead = 0.5; // + N%
        $lengthThresholds = array(255, 1000, 4000);

        $possibleLength = MathHelper::max(
            (isset($column->type->length) ? $column->type->length : 0),
            (isset($length) ? $length : 0));

        $maxlength = $this->maxLength4VariableLengthString();

        $isMatchFound = FALSE;
        foreach ($lengthThresholds as $lengthThreshold) {
            if ($lengthThreshold > $maxlength) {
                break;
            }
            if (($possibleLength * (1.0 + $overhead)) <= $lengthThreshold) {
                $possibleLength = $lengthThreshold;
                $isMatchFound = TRUE;
                break;
            }
        }

        if (!$isMatchFound) {
            $possibleLength = NULL;
        }

        $column->type->length = $possibleLength;

        if ($isMatchFound) {
            $this->assembleVariableLengthString($column);
        }
        else {
            $this->assembleLongString($column);
        }
    }

    protected function assembleFixedLengthString(ColumnMetaData $column) {
        $column->type->databaseType = 'CHAR';

        if (isset($column->type->length)) {
            $column->type->databaseType .= '(' . $column->type->length . ')';
        }
    }

    protected function prepareFixedLengthString(ColumnMetaData $column, $length = NULL) {
        $column->type->length = $length;

        $this->assembleFixedLengthString($column);
    }

    // *****************************************************************************************************************************
    //      Type: Numeric
    // *****************************************************************************************************************************
    protected function assembleTinyInteger(ColumnMetaData $column) {
        $this->assembleInteger($column);
    }

    protected function assembleSmallInteger(ColumnMetaData $column) {
        $this->assembleInteger($column);
    }

    protected function assembleInteger(ColumnMetaData $column) {
        $column->type->databaseType = 'INTEGER';
    }

    abstract protected function assembleBigInteger(ColumnMetaData $column);

    protected function prepareInteger(ColumnMetaData $column, $precision = NULL) {
        $column->type->precision = MathHelper::max($column->type->length, $column->type->precision, $precision);
        if (isset($column->type->precision)) {
            if ($column->type->precision < 3) {
                $this->assembleTinyInteger($column);
            }
            elseif ($column->type->precision < 5) {
                $this->assembleSmallInteger($column);
            }
            elseif ($column->type->precision < 10) {
                $this->assembleInteger($column);
            }
            else {
                $this->assembleBigInteger($column);
            }
        }
        else {
            $this->assembleInteger($column);
        }
    }

    protected function assembleNumber(ColumnMetaData $column, $selectedScale) {
        $column->type->databaseType = "DECIMAL({$column->type->precision}, $selectedScale)";
    }

    protected function prepareNumber(ColumnMetaData $column, $precision = NULL, $scale = NULL) {
        $possiblePrecision = isset($precision) ? $precision : 15;
        if (!isset($column->type->precision) || ($column->type->precision < $possiblePrecision)) {
            $column->type->precision = $possiblePrecision;
        }

        $possibleScale = isset($scale) ? $scale : 2;
        if (!isset($column->type->scale) || ($column->type->scale < $possibleScale)) {
            $column->type->scale = $possibleScale;
        }

        // adjusting scale for 'percent' type
        $selectedScale = $column->type->scale;
        if ($column->type->applicationType == PercentDataTypeHandler::$DATA_TYPE) {
            $selectedScale += 2;
        }

        $this->assembleNumber($column, $selectedScale);
    }

    // *****************************************************************************************************************************
    //      Type: Date & Time
    // *****************************************************************************************************************************
    protected function assembleTime(ColumnMetaData $column) {
        $column->type->databaseType = 'TIME';
    }

    protected function assembleDate(ColumnMetaData $column) {
        $column->type->databaseType = 'DATE';
    }

    protected function assembleDateTime(ColumnMetaData $column) {
        $column->type->databaseType = 'TIMESTAMP';
    }

    protected function prepareDate(ColumnMetaData $column, $isDate = TRUE, $isTime = FALSE) {
        if ($isDate) {
            if ($isTime) {
                $this->assembleDateTime($column);
            }
            else {
                $this->assembleDate($column);
            }
        }
        else {
            $this->assembleTime($column);
        }
    }

    // *****************************************************************************************************************************
    //      Column
    // *****************************************************************************************************************************
    /*
     * Maps column application data type to database specific type
     */
    protected function prepareColumnDatabaseType(DataSourceHandler $handler, ColumnMetaData $column) {
        if (isset($column->type->applicationType)) {
            $storageDataType = NULL;

            list($datasetName) = ReferencePathHelper::splitReference($column->type->applicationType);
            if (isset($datasetName)) {
                $storageDataType = Sequence::getSequenceColumnType()->applicationType;
            }
            else {
                $datatypeHandler = DataTypeFactory::getInstance()->getHandler($column->type->applicationType);
                $storageDataType = $datatypeHandler->getStorageDataType();
            }

            switch ($storageDataType) {
                case StringDataTypeHandler::$DATA_TYPE:
                    break;
                case IntegerDataTypeHandler::$DATA_TYPE:
                    $this->prepareInteger($column);
                    break;
                case NumberDataTypeHandler::$DATA_TYPE:
                case CurrencyDataTypeHandler::$DATA_TYPE:
                case PercentDataTypeHandler::$DATA_TYPE:
                    $this->prepareNumber($column);
                    break;
                case BooleanDataTypeHandler::$DATA_TYPE:
                    // calculating length of mapping of TRUE and FALSE values
                    $valueTrue = $handler->castValue(BooleanDataTypeHandler::$DATA_TYPE, TRUE);
                    $valueFalse = $handler->castValue(BooleanDataTypeHandler::$DATA_TYPE, FALSE);
                    // length of the field depends on length of the mappings
                    $lengthValueTrue = strlen($valueTrue);
                    $lengthValueFalse = strlen($valueFalse);
                    $length = MathHelper::max($lengthValueTrue, $lengthValueFalse);
                    // detecting type for each value and selecting primary type
                    $datatype = DataTypeFactory::getInstance()->selectDataType(
                        array(
                            DataTypeFactory::getInstance()->autoDetectDataType($valueTrue),
                            DataTypeFactory::getInstance()->autoDetectDataType($valueFalse)));
                    // for numeric values we use integer storage type, for rest - string
                    if ($datatype === IntegerDataTypeHandler::$DATA_TYPE) {
                        $this->prepareInteger($column, $length);
                    }
                    elseif ($lengthValueTrue === $lengthValueFalse) {
                        $this->prepareFixedLengthString($column, $length);
                    }
                    else {
                        $this->prepareVariableLengthString($column, $length);
                    }
                    break;
                case DateTimeDataTypeHandler::$DATA_TYPE:
                    $this->prepareDate($column, TRUE, TRUE);
                    break;
                case DateDataTypeHandler::$DATA_TYPE:
                    $this->prepareDate($column);
                    break;
                case TimeDataTypeHandler::$DATA_TYPE:
                    $this->prepareDate($column, FALSE, TRUE);
                    break;
                default:
                    throw new UnsupportedOperationException(t(
                        "Unsupported data type for '@columnName' column: @columnType",
                        array('@columnName' => $column->publicName, '@columnType' => $column->type->applicationType)));
            }
        }

        if (!isset($column->type->databaseType)) {
            $this->prepareVariableLengthString($column);
        }

        return $column->type->databaseType;
    }

    protected function prepareCreateTableStatement4Column(DataSourceHandler $handler, ColumnMetaData $column) {
        return $column->name . ' ' . $this->prepareColumnDatabaseType($handler, $column) . ($column->isKey() ? ' NOT NULL' : '');
    }

    // *****************************************************************************************************************************
    //      Table
    // *****************************************************************************************************************************
    protected function assembleTableColumns(DataSourceHandler $handler, DatasetMetaData $dataset, $indent, &$sql) {
        $columnSQLs = NULL;
        foreach ($dataset->getColumns() as $column) {
            $columnSQLs[$column->columnIndex] = $this->prepareCreateTableStatement4Column($handler, $column);
        }
        if (!isset($columnSQLs)) {
            throw new IllegalArgumentException(t(
                "'@datasetName' dataset must have at least one column to create permanent storage",
                array('@datasetName' => $dataset->publicName)));
        }

        // sorting columns by column index
        ksort($columnSQLs);

        $sql .= $indent . implode(",\n$indent", $columnSQLs);
    }

    protected function assembleTableSystemColumns(DataSourceHandler $handler, DatasetMetaData $dataset, $indent, &$sql) {
        // preparing 'version' system column
        $columnVersion = new ColumnMetaData();
        $columnVersion->name = DatasetSystemColumnNames::VERSION;
        $columnVersion->description = t('System column to store version of a record');
        $columnVersion->type->applicationType = IntegerDataTypeHandler::$DATA_TYPE;
        $sql .= ",\n$indent" . $this->prepareCreateTableStatement4Column($handler, $columnVersion);

        // preparing 'state' system column
        $columnState = new ColumnMetaData();
        $columnState->name = DatasetSystemColumnNames::STATE;
        $columnState->description = t('System column to store internal state of a record');
        $columnState->type->applicationType = IntegerDataTypeHandler::$DATA_TYPE;
        $sql .= ",\n$indent" . $this->prepareCreateTableStatement4Column($handler, $columnState);
    }

    protected function assemblePrimaryKeyConstraint(DataSourceHandler $handler, DatasetMetaData $dataset, $indent, &$sql) {
        $primaryKeyConstraintColumns = NULL;
        foreach ($dataset->getColumns() as $column) {
            if ($column->isKey()) {
                $primaryKeyConstraintColumns[$column->columnIndex] = $column->name;
            }
        }

        if (isset($primaryKeyConstraintColumns)) {
            // sorting columns by column index
            ksort($primaryKeyConstraintColumns);
            $sql .= ",\n{$indent}CONSTRAINT pk_{$dataset->source} PRIMARY KEY (" . implode(', ', $primaryKeyConstraintColumns) . ')';
        }
    }

    protected function assembleUniqueKeyConstraints(DataSourceHandler $handler, DatasetMetaData $dataset, $indent, &$sql) {
        foreach ($dataset->getColumns() as $column) {
            $columnName = $column->name;

            if (!$column->isUnique()) {
                continue;
            }

            // if the column is also a primary key we do not need to create another constraint
            if ($column->isKey()) {
                continue;
            }

            $sql .= ",\n{$indent}CONSTRAINT uk_{$dataset->source}_{$columnName} UNIQUE ($columnName)";
        }
    }

    protected function assembleForeignKeyConstraints(DataSourceHandler $handler, DatasetMetaData $dataset, $indent, &$sql) {
        $metamodel = data_controller_get_metamodel();

        foreach ($dataset->getColumns() as $column) {
            $columnName = $column->name;

            if (!isset($column->type->sourceApplicationType)) {
                continue;
            }

            // the column has to contain a reference to another dataset
            $dimensionLookupHandler = DimensionLookupFactory::getInstance()->getHandler($column->type->sourceApplicationType);
            list($referencedDatasetName) = $dimensionLookupHandler->adjustReferencePointColumn($metamodel, $dataset->name, $column->name);
            if ($dataset->name == $referencedDatasetName) {
                continue;
            }

            $referencedDataset = $metamodel->getDataset($referencedDatasetName);
            // we can create a foreign key constraint referenced to a table only
            $referencedDatasetSourceType = DatasetTypeHelper::detectDatasetSourceType($referencedDataset);
            if ($referencedDatasetSourceType != DatasetTypeHelper::DATASET_SOURCE_TYPE__TABLE) {
                continue;
            }

            $referencedOwner = NULL;
            if ($dataset->datasourceName != $referencedDataset->datasourceName) {
                // if we cannot join datasets we cannot create a foreign key constraint
                $datasourceQueryHandler = DataSourceQueryFactory::getInstance()->getHandler($handler->getDataSourceType());
                if (!$datasourceQueryHandler->isJoinSupported($dataset->datasourceName, $referencedDataset->datasourceName)) {
                    continue;
                }

                $referencedOwner = $handler->getDataSourceOwner($referencedDataset->datasourceName);
            }

            $referencedTableName = $referencedDataset->source;
            $referencedColumnName = $referencedDataset->getKeyColumn()->name;

            $sql .= ",\n{$indent}CONSTRAINT fk_{$dataset->source}_{$columnName} FOREIGN KEY ($columnName) REFERENCES "
                . (isset($referencedOwner) ? ($referencedOwner . '.') : '')
                . "$referencedTableName ({$referencedColumnName})";
        }
    }

    protected function assembleTableEntities(DataSourceHandler $handler, DatasetMetaData $dataset, $indent, &$sql) {}

    protected function assembleTableOptions(DataSourceHandler $handler, DatasetMetaData $dataset, $indent, &$sql) {}

    protected function prepareCreateTableStatement(DataSourceHandler $handler, DatasetMetaData $dataset) {
        $indent = str_pad('', Statement::$INDENT_NESTED);

        $sql = "CREATE TABLE {$dataset->source} (\n";

        $this->assembleTableColumns($handler, $dataset, $indent, $sql);
        $this->assembleTableSystemColumns($handler, $dataset, $indent, $sql);
        $this->assemblePrimaryKeyConstraint($handler, $dataset, $indent, $sql);
        $this->assembleUniqueKeyConstraints($handler, $dataset, $indent, $sql);
        $this->assembleForeignKeyConstraints($handler, $dataset, $indent, $sql);

        $this->assembleTableEntities($handler, $dataset, $indent, $sql);

        $sql .= "\n)";

        $this->assembleTableOptions($handler, $dataset, $indent, $sql);

        return $sql;
    }
}
