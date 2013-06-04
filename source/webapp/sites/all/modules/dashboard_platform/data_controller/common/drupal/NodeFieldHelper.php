<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


function get_node_field_value($node, $propertyName, $index = 0, $storageSuffixName = 'value', $required = FALSE) {
    $value = NULL;

    $nodePropertyValue = isset($node->$propertyName) ? $node->$propertyName : NULL;
    if (isset($nodePropertyValue[$node->language][$index][$storageSuffixName])) {
        $value = $nodePropertyValue[$node->language][$index][$storageSuffixName];
        if (is_string($value)) {
            $value = trim($value);
            if (strlen($value) === 0) {
                $value = NULL;
            }
        }
    }

    if ($required && !isset($value)) {
        LogHelper::log_error($node);
        throw new IllegalArgumentException(t(
            '@propertyName@index has not been set for the node: @nodeId',
            array(
                '@nodeId' => $node->nid,
                '@propertyName' => $propertyName,
                '@index' => (($index == 0) ? '' : t('[@index]', array('@index' => $index))))));
    }

    return $value;
}

function get_node_field_node_ref($node, $propertyName, $index = 0, $storageSuffixName = 'nid', $required = FALSE) {
    return get_node_field_int_value($node, $propertyName, $index, $storageSuffixName, $required);
}

function get_node_field_int_value($node, $propertyName, $index = 0, $storageSuffixName = 'value', $required = FALSE) {
    $value = get_node_field_value($node, $propertyName, $index, $storageSuffixName, $required);
    if (isset($value)) {
        $value = (int) $value;
    }

    return $value;
}

function get_node_field_boolean_value($node, $propertyName, $index = 0, $storageSuffixName = 'value', $default = FALSE) {
    $value = get_node_field_int_value($node, $propertyName, $index, $storageSuffixName);

    return isset($value) ? ($value == 1) : $default;
}

function get_node_field_object_value($node, $propertyName, $index = 0, $storageSuffixName = 'value', $required = FALSE) {
    $value = get_node_field_value($node, $propertyName, $index, $storageSuffixName, $required);
    if (isset($value)) {
        $value = json_decode($value, FALSE);
    }

    return $value;
}
