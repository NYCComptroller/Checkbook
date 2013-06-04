<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class StatementLogMessageKeeper extends AbstractLogMessageListener {

    public static $statements = NULL;

    public static function reset() {
        self::$statements = NULL;
    }

    public function log($level, &$message) {
        if ($message instanceof StatementLogMessage) {
            $statementLogMessage = $message;

            if (is_array($statementLogMessage->statement)) {
                ArrayHelper::mergeArrays(self::$statements, $statementLogMessage->statement);
            }
            else {
                self::$statements[$statementLogMessage->type][] = $statementLogMessage->statement;
            }
        }
    }
}
