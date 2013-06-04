<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




class DefaultPropertyBasedComparator extends AbstractPropertyBasedComparator {

    protected function getProperty($record, $propertyName) {
        return isset($record)
            ? (is_array($record)
                ? (isset($record[$propertyName]) ? $record[$propertyName] : NULL)
                : $record->$propertyName)
            : NULL;
    }
}
