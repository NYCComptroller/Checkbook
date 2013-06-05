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


class SimpleDimensionLookupHandler extends AbstractDimensionLookupHandler {

    function prepareLookupValue($value) {
        $lookupValue = new DimensionLookupHandler__LookupValue();
        // for this dimension lookup handler we do not need to support any lookups. $value is the identifier
        $lookupValue->identifier = $value;

        return $lookupValue;
    }

    public function prepareDatasetColumnLookupIds($datasetName, ColumnMetaData $column, array &$lookupValues) {}

    public function prepareDimension(MetaModel $metamodel, DatasetMetaData $dataset, $columnName, CubeMetaData $cube) {
        $column = $dataset->getColumn($columnName);
        $dimension = $cube->getDimension($columnName);

        $level = $dimension->registerLevel($column->name);
        $level->publicName = $column->publicName;
        $level->sourceColumnName = $column->name;
    }
}
