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


class LookupDatasetColumnDimensionLookupHandler extends AbstractDimensionLookupHandler {

    protected $datasetName = NULL;
    protected $columnName = NULL;

    public function __construct($datatype) {
        parent::__construct($datatype);

        list($this->datasetName, $this->columnName) = ReferencePathHelper::splitReference($datatype);
    }

    public function prepareLookupValue($value) {
        $lookupValue = new DimensionLookupHandler__LookupValue();
        $lookupValue->setProperty($this->columnName, $value);

        return $lookupValue;
    }

    public function prepareDatasetColumnLookupIds($datasetName, ColumnMetaData $column, array &$lookupValues) {
        $metamodel = data_controller_get_metamodel();

        $lookupDataset = $metamodel->getDataset($this->datasetName);
        $lookupColumn = $lookupDataset->getColumn($this->columnName);

        $cubeName = $lookupDataset->name;
        $lookupCube = $metamodel->getCube($cubeName);

        $lookupHandler = DimensionLookupFactory::getInstance()->getHandler($lookupColumn->type->applicationType);
        list($adjustedDatasetName, $adjustedColumnName) = $lookupHandler->adjustReferencePointColumn($metamodel, $lookupCube->sourceDatasetName, $this->columnName);

        $adjustedDataset = $metamodel->getDataset($adjustedDatasetName);
        $adjustedColumn = $adjustedDataset->getColumn($adjustedColumnName);

        // updating lookup values
        if ($this->columnName != $adjustedColumnName) {
            foreach ($lookupValues as $lookupValue) {
                $value = $lookupValue->getProperty($this->columnName);
                $lookupValue->setProperty($this->columnName, NULL);
                $lookupValue->setProperty($adjustedColumnName, $value);
            }
        }

        // do not use prepareIdentifiers() because that function will insert new records into the lookup dataset
        $this->loadIdentifiers($adjustedDataset->name, array($adjustedColumn), $lookupValues);
    }

    public function prepareDimension(MetaModel $metamodel, DatasetMetaData $dataset, $columnName, CubeMetaData $cube) {
        $column = $dataset->getColumn($columnName);
        $sourceDatasetColumn = $cube->sourceDataset->getColumn($columnName);
        $dimension = $cube->getDimension($columnName);

        $referencedCube = $metamodel->getCube($this->datasetName);

        // preparing level properties
        $level = $dimension->registerLevel($columnName);
        $level->publicName = $column->publicName;
        $level->sourceColumnName = $columnName;

        // preparing level dataset
        $level->dataset = $metamodel->getDataset($this->datasetName);
        $level->datasetName = $level->dataset->name;

        // cube source dataset column contains a reference to lookup
        $sourceDatasetColumn->initializeTypeFrom(Sequence::getSequenceColumnType());

        // adding a reference to level dataset
        $referenceName = $referencedCube->sourceDatasetName;
        $metamodel->registerSimpleReferencePoint($referenceName, $referencedCube->sourceDatasetName, NULL);
        $metamodel->registerSimpleReferencePoint($referenceName, $cube->sourceDatasetName, $columnName);
        // ... to support retrieving properties of the level dataset
        $metamodel->registerSimpleReferencePoint($referenceName, $level->datasetName, $level->dataset->getKeyColumn()->name);
    }

    public function adjustReferencePointColumn(AbstractMetaModel $metamodel, $datasetName, $columnName) {
        // FIXME we should work only with one way to find a cube
        $cube = $metamodel->findCubeByDatasetName($this->datasetName);
        if (!isset($cube)) {
            $cube = $metamodel->getCube($this->datasetName);
        }

        $adjustedDatasetName = $cube->sourceDatasetName;
        $adjustedDataset = $metamodel->getDataset($adjustedDatasetName);

        $adjustedColumnName = $adjustedDataset->getKeyColumn()->name;

        $shared = TRUE;
        return array($adjustedDatasetName, $adjustedColumnName, $shared);
    }
}
