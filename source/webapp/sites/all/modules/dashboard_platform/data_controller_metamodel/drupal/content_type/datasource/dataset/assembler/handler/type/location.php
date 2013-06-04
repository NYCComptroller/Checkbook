<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




// TODO replace these constants with module-specific once they are available
define('LOCATION_FIELD_DO_NOT_COLLECT', 0);
define('LOCATION_FIELD_COLLECT', 1);
define('LOCATION_FIELD_FORCE_DEFAULT', 4);

function ContentTypeDatasetSourceAssembler_location(
        DatasetSourceAssembler $assembler,
        DataControllerCallContext $callcontext, $parameterNames,
        Statement $statement, $tableIndex, $supportedField) {

    $tableAlias = $assembler->prepareTableAlias($tableIndex);

    $field = $assembler->config->drupal['fields'][$supportedField->original_name];

    // preparing list of parameters which should be supported by the location
    // if parameter names are not defined we should return all available parameters
    $locationParameterNames = NULL;
    if (isset($parameterNames)) {
        $parameterNamePrefix = $supportedField->name . '_';
        foreach ($parameterNames as $parameterName) {
            if (strpos($parameterName, $parameterNamePrefix) === 0) {
                $locationParameterName = substr($parameterName, strlen($parameterNamePrefix));
                $locationParameterNames[] = $locationParameterName;
            }
        }
        if (!isset($locationParameterNames)) {
            return;
        }
    }

    $locationTableAlias = $assembler->prepareColumnAlias($supportedField->name);

    $locationTableSection = new TableSection('location', $locationTableAlias);
    $statement->tables[] = $locationTableSection;

    // linking the location table with 'parent' table
    $locationTableSection->conditions[] = new JoinConditionSection(
        'lid', new TableColumnConditionSectionValue($tableAlias, $supportedField->column));

    // adding selected columns
    foreach ($field['location_settings']['form']['fields'] as $locationFieldName => $locationFieldConfig) {
        $collectionFlag = $locationFieldConfig['collect'];

        // these fields should contain values
        if (($collectionFlag != LOCATION_FIELD_COLLECT) && ($collectionFlag != LOCATION_FIELD_FORCE_DEFAULT)) {
            continue;
        }

        $mappedColumnNames = NULL;
        if ($locationFieldName === 'locpick') {
            $mappedColumnNames[] = 'latitude';
            $mappedColumnNames[] = 'longitude';
        }
        else {
            $mappedColumnNames[] = $locationFieldName;
        }

        foreach ($mappedColumnNames as $mappedColumnName) {
            if (isset($locationParameterNames) && (array_search($mappedColumnName, $locationParameterNames) === FALSE)) {
                continue;
            }

            $locationTableSection->columns[] = new ColumnSection($mappedColumnName, $locationTableAlias . '_' . $mappedColumnName);
        }
    }
}

