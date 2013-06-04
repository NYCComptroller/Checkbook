<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
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
