<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
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
