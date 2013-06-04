<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class ArrayLogMessageSlicer extends AbstractLogMessageListener {

    public static $LOGGED_ELEMENTS__MAXIMUM = 50; // NULL - logging whole array

    public function log($level, &$message) {
        if (is_array($message) && isset(self::$LOGGED_ELEMENTS__MAXIMUM)) {
            $count = count($message);
            if ($count > self::$LOGGED_ELEMENTS__MAXIMUM) {
                $slice = array_slice($message, 0, self::$LOGGED_ELEMENTS__MAXIMUM);
                $slice[] = t(
                    '@deletedElementCount ELEMENTS WERE DELETED TO SAVE LOG SPACE',
                    array('@deletedElementCount' => ($count - self::$LOGGED_ELEMENTS__MAXIMUM)));

                $message = $slice;
            }
        }
    }
}
