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


define('PUBLISHED_ONLY', TRUE);
define('INCLUDE_UNPUBLISHED', FALSE);

define('LOAD_ENTITY', TRUE);
define('LOAD_ENTITY_ID_ONLY', FALSE);


function prepare_entity_query_4_node_type($nodeType, $publishedOnly = PUBLISHED_ONLY) {
    $query = new EntityFieldQuery();
    $query->entityCondition('entity_type', 'node')->propertyCondition('type', $nodeType);
    if ($publishedOnly) {
        $query->propertyCondition('status', NODE_PUBLISHED);
    }
    // Neutralize the 'entity_field_access' query tag added by
    // field_sql_storage_field_storage_query(). The result cannot depend on the
    // access grants of the current user.
    $query->addTag('DANGEROUS_ACCESS_CHECK_OPT_OUT');

    return $query;
}

function process_entity_query_result(array $entities = NULL, $loadNodes = LOAD_ENTITY_ID_ONLY) {
    $nids = isset($entities['node']) ? array_keys($entities['node']) : array();

    return ($loadNodes == LOAD_ENTITY) ? node_load_multiple($nids) : $nids;
}

function execute_entity_query_4_node_type($nodeType, $loadNodes = LOAD_ENTITY_ID_ONLY, $publishedOnly = PUBLISHED_ONLY) {
    $query = prepare_entity_query_4_node_type($nodeType, $publishedOnly);
    $entities = $query->execute();
    return process_entity_query_result($entities, $loadNodes);
}
