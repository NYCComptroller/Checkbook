<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
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
