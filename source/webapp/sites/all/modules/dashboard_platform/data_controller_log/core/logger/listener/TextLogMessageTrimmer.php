<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class TextLogMessageTrimmer extends AbstractLogMessageListener {

    public static $LOGGED_TEXT_LENGTH__MAXIMUM = 512; // NULL - logging string

    public function log($level, &$message) {
        if (is_string($message) && isset(self::$LOGGED_TEXT_LENGTH__MAXIMUM)) {
            $length = strlen($message);
            if ($length > self::$LOGGED_TEXT_LENGTH__MAXIMUM) {
                $message = substr($message, 0, self::$LOGGED_TEXT_LENGTH__MAXIMUM)
                    . t(' ... @trimmedCharacterLength more character(s)', array('@trimmedCharacterLength' => ($length - self::$LOGGED_TEXT_LENGTH__MAXIMUM)));
            }
        }
    }
}
