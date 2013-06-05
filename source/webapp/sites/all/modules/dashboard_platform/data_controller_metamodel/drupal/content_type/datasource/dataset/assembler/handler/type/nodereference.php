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




function ContentTypeDatasetSourceAssembler_nodereference(
        DatasetSourceAssembler $assembler,
        DataControllerCallContext $callcontext, $parameterNames,
        $statement, $tableIndex, $supportedField) {

    $tableAlias = $assembler->prepareTableAlias($tableIndex);

    $field = $assembler->config->drupal['fields'][$supportedField->original_name];

    // preparing referenceable content type
    $referenceableContentTypes = array_keys(array_filter($field['referenceable_types']));
    if (count($referenceableContentTypes) != 1) {
        $message = t(
        	"Unsupported configuration for referenced types for '@supportedFieldName' field: [@contentTypes]",
            array('@supportedFieldName' => $supportedField->name, '@contentTypes' => implode(', ', $referenceableContentTypes)));
        LogHelper::log_warn($message);
        return;
    }
    $referenceableContentType = $referenceableContentTypes[0];
    $referenceableContentTypeKey = 'nid';

    // preparing list of parameters which should be supported by the referenceable content type
    // if parameter names are not defined we should return all available parameters
    $contentTypeParameterNames = NULL;
    if (isset($parameterNames)) {
        $parameterNamePrefix = $supportedField->name . '_';
        foreach ($parameterNames as $parameterName) {
            if (strpos($parameterName, $parameterNamePrefix) === 0) {
                $contentTypeParameterName = substr($parameterName, strlen($parameterNamePrefix));
                $contentTypeParameterNames[] = $contentTypeParameterName;
            }
        }
        if (!isset($contentTypeParameterNames)) {
            return;
        }
    }
    if (isset($contentTypeParameterNames)) {
        // we will use the field to link with parent content type
        $contentTypeParameterNames[] = $referenceableContentTypeKey;
    }

    // adding check to prevent recursive/circular references
    if ($assembler->registerContentTypeInStack($callcontext, $referenceableContentType)) {
        // requesting SQL sections related to the content type
        $contentTypeDatasetName = DrupalContentTypeMetaModelLoader::getDatasetName($referenceableContentType);
        $contentTypeDataset = $callcontext->metamodel->getDataset($contentTypeDatasetName);

        $contentTypeTableAliasPrefix = $assembler->prepareColumnAlias($supportedField->name);

        $contentTypeAssembler = new ContentTypeDatasetSourceAssembler(
            $contentTypeDataset->source->assembler->config, $contentTypeTableAliasPrefix);
        $contentTypeStatement = $contentTypeAssembler->assemble($callcontext, $contentTypeParameterNames);

        // adding condition to join with 'main' statement
        $contentTypeTableSection = $contentTypeStatement->tables[0];
        $contentTypeTableSection->conditions[] = new JoinConditionSection(
            $referenceableContentTypeKey,
            new TableColumnConditionSectionValue($tableAlias, $supportedField->column));
        // merging with 'main' statement
        $statement->merge($contentTypeStatement);

        $assembler->unregisterContentTypeFromStack($callcontext, $referenceableContentType);
    }
}
