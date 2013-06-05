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
