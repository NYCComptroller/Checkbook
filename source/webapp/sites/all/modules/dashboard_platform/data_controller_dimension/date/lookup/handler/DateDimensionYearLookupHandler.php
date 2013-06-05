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
