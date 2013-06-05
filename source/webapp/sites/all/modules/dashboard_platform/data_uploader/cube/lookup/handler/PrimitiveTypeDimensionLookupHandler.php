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


class PrimitiveTypeDimensionLookupHandler extends AbstractDimensionLookupHandler {

    public function prepareLookupValue($value) {
        $lookupValue = new DimensionLookupHandler__LookupValue();
        $lookupValue->setProperty(StarSchemaNamingConvention::$COLUMN_NAME__ATTRIBUTE_LOOKUP_VALUE, $value);

        return $lookupValue;
    }

    public function prepareDatasetColumnLookupIds($datasetName, ColumnMetaData $column, array &$lookupValues) {
        $lookupDatasetName = StarSchemaNamingConvention::getAttributeRelatedName($datasetName, $column->name);
        $sequenceName = $lookupDatasetName;

        $lookupValueColumn = new ColumnMetaData();
        $lookupValueColumn->name = StarSchemaNamingConvention::$COLUMN_NAME__ATTRIBUTE_LOOKUP_VALUE;
        $lookupValueColumn->initializeTypeFrom($column->type);

        $this->prepareIdentifiers($lookupDatasetName, array($lookupValueColumn), NULL, $sequenceName, $lookupValues);
    }

    public function prepareDimension(MetaModel $metamodel, DatasetMetaData $dataset, $columnName, CubeMetaData $cube) {
        $column = $dataset->getColumn($columnName);
        $sourceDatasetColumn = $cube->sourceDataset->getColumn($columnName);
        $dimension = $cube->getDimension($columnName);

        // preparing level properties
        $level = $dimension->registerLevel($columnName);
        $level->publicName = $column->publicName;
        $level->sourceColumnName = $columnName;
        $level->datasetName = StarSchemaNamingConvention::getAttributeRelatedName($dataset->name, $columnName);

        // preparing level dataset
        $level->dataset = new DatasetMetaData();
        $level->dataset->name = $level->datasetName;
        $level->dataset->publicName = $dataset->publicName . " [$column->publicName]";
        $level->dataset->description = t("Lookup table to store unique values from '@columnName' column", array('@columnName' => $column->publicName));
        $level->dataset->datasourceName = $dataset->datasourceName;
        $level->dataset->source = StarSchemaNamingConvention::getAttributeRelatedName($dataset->source, $columnName);
        $level->dataset->storageType = DatasetMetaData::$STORAGE_TYPE__DATA_CONTROLLER_MANAGED;
        $level->dataset->system = TRUE;
        // adding level dataset aliases
        if (isset($dataset->aliases)) {
            foreach ($dataset->aliases as $alias) {
                $level->dataset->aliases[] = StarSchemaNamingConvention::getAttributeRelatedName($alias, $columnName);
            }
        }

        // adding key column
        $levelDatasetKeyColumn = $level->dataset->registerColumn($columnName);
        $levelDatasetKeyColumn->publicName = $column->publicName;
        $levelDatasetKeyColumn->description = t("System generated ID to identify each unique value from '@columnName' column", array('@columnName' => $column->publicName));
        $levelDatasetKeyColumn->initializeTypeFrom(Sequence::getSequenceColumnType());
        $levelDatasetKeyColumn->key = TRUE;
        $levelDatasetKeyColumn->visible = FALSE;
        // adding 'value' column
        $levelDatasetValueColumn = $level->dataset->registerColumn(StarSchemaNamingConvention::$COLUMN_NAME__ATTRIBUTE_LOOKUP_VALUE);
        $levelDatasetValueColumn->publicName = $column->publicName;
        $levelDatasetValueColumn->description = t("Actual value from '@columnName' column", array('@columnName' => $column->publicName));
        $levelDatasetValueColumn->initializeTypeFrom($column->type);

        // cube source dataset column contains a reference to lookup
        $sourceDatasetColumn->initializeTypeFrom(Sequence::getSequenceColumnType());

        // marking that the level dataset object contains complete meta data & registering it in meta model
        $level->dataset->markAsComplete();
        $metamodel->registerDataset($level->dataset);

        // adding a reference to level dataset
        $referenceName = $level->datasetName;
        $metamodel->registerSimpleReferencePoint($referenceName, $level->datasetName, $columnName);
        $metamodel->registerSimpleReferencePoint($referenceName, $cube->sourceDatasetName, $columnName);
    }

    public function unprepareDimension(MetaModel $metamodel, DatasetMetaData $dataset, $columnName) {
        $datasetName = StarSchemaNamingConvention::getAttributeRelatedName($dataset->name, $columnName);

        $metamodel->unregisterDataset($datasetName);
    }

    public function adjustReferencePointColumn(AbstractMetaModel $metamodel, $datasetName, $columnName) {
        // FIXME we should work only with one way to find a cube
        $cube = $metamodel->findCubeByDatasetName($datasetName);
        if (!isset($cube)) {
            $cube = $metamodel->getCube($datasetName);
        }

        $dimension = $cube->getDimension($columnName);
        $level = $dimension->getLevel($columnName);

        $shared = FALSE;

        return array($level->datasetName, StarSchemaNamingConvention::$COLUMN_NAME__ATTRIBUTE_LOOKUP_VALUE, $shared);
    }
}
