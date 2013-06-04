<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class DateDimensionYearLookupHandler extends AbstractDateDimensionLookupHandler {

    protected static $COLUMN_NAME__ATTRIBUTE_LOOKUP_VALUE = 'entry_year';

    public function prepareLookupValue($value) {
        $lookupValue = new DimensionLookupHandler__LookupValue();
        $lookupValue->setProperty(self::$COLUMN_NAME__ATTRIBUTE_LOOKUP_VALUE, $value);

        return $lookupValue;
    }

    public function prepareDatasetColumnLookupIds($datasetName, ColumnMetaData $column, array &$lookupValues) {
        $lookupValueColumn = new ColumnMetaData();
        $lookupValueColumn->name = self::$COLUMN_NAME__ATTRIBUTE_LOOKUP_VALUE;
        $lookupValueColumn->initializeTypeFrom($column->type);

        $this->prepareIdentifiers(DateDimensionDatasetNames::YEARS, array($lookupValueColumn), NULL, self::$SEQUENCE_NAME__TIME, $lookupValues);
    }


    // *****************************************************************************************************************************
    //
    // Supporting functions to implement prepareDimension()
    //
    // *****************************************************************************************************************************
    protected function prepareYearLevel(DimensionMetaData $dimension, ColumnMetaData $column) {
        $level = $dimension->registerLevel(DateDimensionLevelNames::YEARS);
        $level->publicName = t('Year');
        $level->sourceColumnName = $column->name;
        $level->datasetName = DateDimensionDatasetNames::LEVEL_YEARS;

        return $level;
    }

    public function prepareDimension(MetaModel $metamodel, DatasetMetaData $dataset, $columnName, CubeMetaData $cube) {
        $column = $dataset->getColumn($columnName);
        $sourceDatasetColumn = $cube->sourceDataset->getColumn($columnName);
        $dimension = $cube->getDimension($columnName);

        $this->prepareYearLevel($dimension, $column);

        // cube source dataset column contains a reference to year identifier
        $sourceDatasetColumn->initializeTypeFrom(Sequence::getSequenceColumnType());

        // adding a reference to date dataset
        $referenceName = DateDimensionDatasetNames::LEVEL_YEARS;
        $metamodel->registerSimpleReferencePoint($referenceName, DateDimensionDatasetNames::LEVEL_YEARS, 'year_id');
        $metamodel->registerSimpleReferencePoint($referenceName, $cube->sourceDatasetName, $columnName);
    }


    // *****************************************************************************************************************************
    //
    // Supporting functions to implement adjustReferencePointColumn()
    //
    // *****************************************************************************************************************************
    public function adjustReferencePointColumn(AbstractMetaModel $metamodel, $datasetName, $columnName) {
        $shared = TRUE;

        return array(DateDimensionDatasetNames::LEVEL_YEARS, self::$COLUMN_NAME__ATTRIBUTE_LOOKUP_VALUE, $shared);
    }
}
