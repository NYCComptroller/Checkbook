<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




abstract class AbstractArrayResultFormatter extends AbstractResultFormatter {

    protected function adjustPropertyName($propertyName) {
        $adjustedPropertyName = parent::adjustPropertyName($propertyName);

        return str_replace(ParameterHelper::$COLUMN_NAME_DELIMITER__CODE, ParameterHelper::$COLUMN_NAME_DELIMITER__DATABASE, $adjustedPropertyName);
    }
}
