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
