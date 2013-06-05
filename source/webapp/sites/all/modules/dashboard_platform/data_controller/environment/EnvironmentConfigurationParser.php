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


class EnvironmentConfigurationParser extends AbstractConfigurationParser {

    protected static $statementFunctions = NULL;

    protected function getStartDelimiter() {
        return '${';
    }

    protected function getEndDelimiter() {
        return '}';
    }

    public function executeStatement(ParserCallbackObject $callbackObject) {
        $statement = $callbackObject->marker;

        $functionName = isset(self::$statementFunctions[$statement]) ? self::$statementFunctions[$statement] : NULL;
        if (!isset($functionName)) {
            $functionName = create_function('', 'return ' . $statement . ';');
            if ($functionName === FALSE) {
                throw new IllegalArgumentException(t('Could not evaluate the statement: @statement', array('@statement' => $statement)));
            }

            self::$statementFunctions[$statement] = $functionName;
        }

        $callbackObject->marker = $functionName();
        $callbackObject->markerUpdated = TRUE;
        $callbackObject->removeDelimiters = TRUE;
    }
}
