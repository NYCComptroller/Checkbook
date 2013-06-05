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


interface DimensionLookupHandler {

    /**
     * Returns an object which is a holder for application value ($value) and corresponding identifier ($obj->identifier)
     * This function may calculate indentifier if there is such feature but should not load it from any data source.
     * Loading/storing is done by different functionality
     *
     * @param $value
     * @return DimensionLookupHandler__AbstractLookupValue
     */
    function prepareLookupValue($value);

    /**
     * Loads indentifiers for lookup values.
     * For missing identifiers this function has to generate them based on application value.
     *
     * @param $datasetName
     * @param ColumnMetaData $column
     * @param array $lookupValues
     */
    function prepareDatasetColumnLookupIds($datasetName, ColumnMetaData $column, array &$lookupValues);

    /**
     * Initializing dimension meta data
     *
     * @param MetaModel $metamodel
     * @param DatasetMetaData $dataset
     * @param string $columnName
     * @param CubeMetaData $cube
     */
    function prepareDimension(MetaModel $metamodel, DatasetMetaData $dataset, $columnName, CubeMetaData $cube);
    /**
     * Deinitializing dimension meta data
     *
     * @param MetaModel $metamodel
     * @param DatasetMetaData $dataset
     * @param string $columnName
     */
    function unprepareDimension(MetaModel $metamodel, DatasetMetaData $dataset, $columnName);

    function adjustReferencePointColumn(AbstractMetaModel $metamodel, $datasetName, $columnName);
}
