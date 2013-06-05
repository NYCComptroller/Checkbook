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


class StarSchemaCubeMetaData extends CubeMetaData {

    // cube generation functionality was split into two functions (registerFromDataset() and initializeFromDataset()) to support intercube references
    public static function registerFromDataset(MetaModel $metamodel, DatasetMetaData $dataset) {
        $cubeName = $dataset->name;

        $cube = new StarSchemaCubeMetaData();

        // preparing cube properties
        $cube->name = $cubeName;
        $cube->publicName = $dataset->publicName;

        // preparing cube source dataset name
        $cube->sourceDatasetName = StarSchemaNamingConvention::getFactsRelatedName($dataset->name);

        $metamodel->registerCube($cube);
    }

    public static function initializeFromDataset(MetaModel $metamodel, DatasetMetaData $dataset) {
        $cubeName = $dataset->name;
        $cube = $metamodel->unregisterCube($cubeName);

        // preparing cube properties
        $cube->description = $dataset->description;

        // preparing cube source dataset
        $sourceDataset = $cube->initiateSourceDataset();
        $sourceDataset->name = $cube->sourceDatasetName;
        $sourceDataset->publicName = $dataset->publicName . ' [facts]';
        $sourceDataset->description = t("Facts table for '@datasetName' dataset", array('@datasetName' => $dataset->publicName));
        $sourceDataset->datasourceName = $dataset->datasourceName;
        $sourceDataset->source = StarSchemaNamingConvention::getFactsRelatedName($dataset->source);
        $sourceDataset->storageType = DatasetMetaData::$STORAGE_TYPE__DATA_CONTROLLER_MANAGED;
        // calculating cube source dataset aliases
        if (isset($dataset->aliases)) {
            foreach ($dataset->aliases as $alias) {
                $sourceDataset->aliases[] = StarSchemaNamingConvention::getFactsRelatedName($alias);
            }
        }

        // preparing cube source dataset columns and cube dimensions
        foreach ($dataset->getColumns() as $column) {
            // we need to preserve original column index
            $sourceDatasetColumn = $sourceDataset->initiateColumn();
            $sourceDatasetColumn->name = $column->name;
            $sourceDatasetColumn->columnIndex = $column->columnIndex;
            $sourceDatasetColumn->publicName = $column->publicName;
            $sourceDatasetColumn->description = $column->description;
            $sourceDatasetColumn->key = $column->key;
            $sourceDatasetColumn->containsUniqueValues = $column->containsUniqueValues;
            $sourceDatasetColumn->visible = $column->visible;
            $sourceDataset->registerColumnInstance($sourceDatasetColumn);

            if (isset($column->columnCategory)) {
                switch ($column->columnCategory) {
                    case DatasetColumnCategories::ATTRIBUTE:
                        $sourceDatasetColumn->description = t("Reference to lookup table for '@columnName' attribute", array('@columnName' => $column->publicName));

                        // preparing dimension
                        $dimension = $cube->registerDimension($column->name);
                        $dimension->publicName = $column->publicName;

                        // storing original column application type to be able to use the same dimension lookup handler
                        $sourceDatasetColumn->type->sourceApplicationType = $column->type->applicationType;
                        $handler = DimensionLookupFactory::getInstance()->getHandler($column->type->applicationType);

                        $handler->prepareDimension($metamodel, $dataset, $column->name, $cube);

                        // ********** adding measure which counts unique values
                        $attributeName = ParameterHelper::assembleParameterName($dimension->name, $dimension->levels[0]->name);
                        // adding measure
                        $measureName = StarSchemaNamingConvention::getAttributeRelatedMeasureName(
                            $attributeName, StarSchemaNamingConvention::$MEASURE_NAME_SUFFIX__DISTINCT_COUNT);
                        $measure = $cube->registerMeasure($measureName);
                        $measure->publicName = t('Distinct Count');
                        $measure->description = t(
                            "System generated measure for '@columnName' column to count dictinct values",
                            array('@functionName' => $measure->publicName, '@columnName' => $dimension->publicName));
                        $measure->function = 'COUNT(DISTINCT ' . ColumnStatementCompositeEntityParser::assembleColumnName($column->name) . ')';
                        $measure->type->applicationType = IntegerDataTypeHandler::$DATA_TYPE;

                        break;
                    case DatasetColumnCategories::FACT:
                        // adding column-specific measures
                        self::registerFactMeasure($cube, $column, 'SUM',
                            // for integer field result of SUM function could be greater than 2147483647 (2^31 - 1)
                            (($column->type->applicationType == IntegerDataTypeHandler::$DATA_TYPE) ? NumberDataTypeHandler::$DATA_TYPE : NULL));
                        self::registerFactMeasure($cube, $column, 'AVG',
                            // for integer field result of AVG function in most cases contains decimals
                            (($column->type->applicationType == IntegerDataTypeHandler::$DATA_TYPE) ? NumberDataTypeHandler::$DATA_TYPE : NULL));
                        self::registerFactMeasure($cube, $column, 'MIN');
                        self::registerFactMeasure($cube, $column, 'MAX');
                        break;
                }
            }

            if (!isset($sourceDatasetColumn->type->applicationType)) {
                // setting column type to original column type
                $sourceDatasetColumn->initializeTypeFrom($column->type);
            }
        }

        // marking that the cube source dataset object contains complete meta data & registering it in meta model
        $sourceDataset->markAsComplete();
        $metamodel->registerDataset($sourceDataset);

        // preparing cube measures
        $measureRecordCount = $cube->registerMeasure(StarSchemaNamingConvention::$MEASURE_NAME__RECORD_COUNT);
        $measureRecordCount->publicName = t('Record Count');
        $measureRecordCount->description = t('System generated measure to count records');
        $measureRecordCount->function = 'COUNT(*)';
        $measureRecordCount->type->applicationType = IntegerDataTypeHandler::$DATA_TYPE;

        $metamodel->registerCube($cube);

        return $cube;
    }

    public static function deinitializeByDataset(MetaModel $metamodel, DatasetMetaData $dataset) {
        $cubeName = $dataset->name;

        $cube = $metamodel->unregisterCube($cubeName);
        $metamodel->unregisterDataset($cube->sourceDatasetName);

        // de-initializing dimensions
        foreach ($dataset->getColumns() as $column) {
            if (isset($column->columnCategory)) {
                switch ($column->columnCategory) {
                    case DatasetColumnCategories::ATTRIBUTE:
                        $handler = DimensionLookupFactory::getInstance()->getHandler($column->type->applicationType);
                        $handler->unprepareDimension($metamodel, $dataset, $column->name);
                        break;
                }
            }
        }
    }

    public static function adjustReferencePointColumn(AbstractMetaModel $metamodel, DatasetReferencePointColumn $referencePointColumn) {
        $datasetName = $referencePointColumn->datasetName;
        $dataset = $metamodel->getDataset($datasetName);

        $column = $dataset->getColumn($referencePointColumn->columnName);

        $handler = DimensionLookupFactory::getInstance()->getHandler($column->type->sourceApplicationType);

        list($adjustedDatasetName, $adjustedColumnName, $shared) = $handler->adjustReferencePointColumn($metamodel, $dataset->name, $referencePointColumn->columnName);

        if ($adjustedDatasetName === $referencePointColumn->datasetName) {
            if ($adjustedColumnName === $referencePointColumn->columnName) {
                // we do not need to change anything
            }
            else {
                throw new UnsupportedOperationException();
            }
        }
        else {
            $referencePointColumn->datasetName = $adjustedDatasetName;
            $referencePointColumn->columnName = $adjustedColumnName;
        }

        $referencePointColumn->shared = $shared;
    }

    protected static function registerFactMeasure(CubeMetaData $cube, ColumnMetaData $column, $functionName, $selectedApplicationDataType = NULL) {
        $measureName = StarSchemaNamingConvention::getFactRelatedMeasureName($column->name, $functionName);

        $measure = $cube->registerMeasure($measureName);
        $measure->publicName = t($functionName);
        $measure->description = t(
            "System generated '@functionName' measure for '@columnName' column",
            array('@functionName' => $measure->publicName, '@columnName' => $column->publicName));
        $measure->function = $functionName . '(' . ColumnStatementCompositeEntityParser::assembleColumnName($column->name) . ')';

        $measure->type->applicationType = isset($selectedApplicationDataType) ? $selectedApplicationDataType : $column->type->applicationType;
        $measure->type->scale = $column->type->scale;
    }
}
