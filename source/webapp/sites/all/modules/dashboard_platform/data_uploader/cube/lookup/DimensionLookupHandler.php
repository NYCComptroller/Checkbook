<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
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
