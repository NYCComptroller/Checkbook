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


class SQL_WildcardOperatorHandler extends SQL_AbstractOperatorHandler {

    public static $MATCH_PATTERN__SINGLE_CHARACTER = '_';
    public static $MATCH_PATTERN__ANY_NUMBER_OF_CHARACTERS = '%';

    public static $CUSTOM_MATCH_PATTERN__SINGLE_CHARACTER = '_';
    public static $CUSTOM_MATCH_PATTERN__ANY_NUMBER_OF_CHARACTERS = '%';

    protected static $ESCAPE_CHARACTER = '|';

    protected function prepareExpression(DataControllerCallContext $callcontext, AbstractRequest $request, $datasetName, $columnName, $columnDataType) {
        $wildcard = $this->getParameterValue('wildcard', TRUE);
        $anyCharactersOnLeft = $this->getParameterValue('anyCharactersOnLeft', FALSE);
        $anyCharactersOnRight = $this->getParameterValue('anyCharactersOnRight', FALSE);

        $searchCharacters = $replaceCharacters = [];
        // escape character
        $searchCharacters[] = self::$ESCAPE_CHARACTER;
        $replaceCharacters[] = self::$ESCAPE_CHARACTER . self::$ESCAPE_CHARACTER;
        // adding user defined match patterns for single character
        if (self::$CUSTOM_MATCH_PATTERN__SINGLE_CHARACTER != self::$MATCH_PATTERN__SINGLE_CHARACTER) {
            $searchCharacters[] = self::$MATCH_PATTERN__SINGLE_CHARACTER;
            $replaceCharacters[] = self::$ESCAPE_CHARACTER . self::$MATCH_PATTERN__SINGLE_CHARACTER;

            $searchCharacters[] = self::$CUSTOM_MATCH_PATTERN__SINGLE_CHARACTER;
            $replaceCharacters[] = self::$MATCH_PATTERN__SINGLE_CHARACTER;
        }
        // adding user defined match patterns for any number of characters
        if (self::$CUSTOM_MATCH_PATTERN__ANY_NUMBER_OF_CHARACTERS != self::$MATCH_PATTERN__ANY_NUMBER_OF_CHARACTERS) {
            $searchCharacters[] = self::$MATCH_PATTERN__ANY_NUMBER_OF_CHARACTERS;
            $replaceCharacters[] = self::$ESCAPE_CHARACTER . self::$MATCH_PATTERN__ANY_NUMBER_OF_CHARACTERS;

            $searchCharacters[] = self::$CUSTOM_MATCH_PATTERN__ANY_NUMBER_OF_CHARACTERS;
            $replaceCharacters[] = self::$MATCH_PATTERN__ANY_NUMBER_OF_CHARACTERS;
        }

        $adjustedWildcard = str_replace($searchCharacters, $replaceCharacters, $wildcard);
        if ($anyCharactersOnLeft) {
            $adjustedWildcard = self::$MATCH_PATTERN__ANY_NUMBER_OF_CHARACTERS . $adjustedWildcard;
        }
        if ($anyCharactersOnRight) {
            $adjustedWildcard .= self::$MATCH_PATTERN__ANY_NUMBER_OF_CHARACTERS;
        }

        $formattedWildcard = $this->datasourceHandler->formatValue(StringDataTypeHandler::$DATA_TYPE, $adjustedWildcard);

        return $this->datasourceHandler->getExtension('formatWildcardValue')->format($this->datasourceHandler, $formattedWildcard, self::$ESCAPE_CHARACTER);
    }
}
