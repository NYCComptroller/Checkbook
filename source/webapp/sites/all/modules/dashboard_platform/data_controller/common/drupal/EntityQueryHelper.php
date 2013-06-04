<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
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
