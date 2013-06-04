<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
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

        $searchCharacters = $replaceCharacters = array();
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
