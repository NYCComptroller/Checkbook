<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class SpecialCharacterResultFormatter extends AbstractResultFormatter {

    protected $propertyNames = NULL;

    public function __construct(array $propertyNames = NULL, ResultFormatter $parent = NULL) {
        parent::__construct($parent);

        ArrayHelper::addUniqueValues($this->propertyNames, $propertyNames);
    }

    public function formatPropertyValue($propertyName, $propertyValue) {
        $formattedPropertyValue = parent::formatPropertyValue($propertyName, $propertyValue);

        if ((!isset($this->propertyNames) || (array_search($propertyName, $this->propertyNames) !== FALSE)) && is_string($formattedPropertyValue)) {
            $formattedPropertyValue = check_plain($formattedPropertyValue);
        }

        return $formattedPropertyValue;
    }
}
