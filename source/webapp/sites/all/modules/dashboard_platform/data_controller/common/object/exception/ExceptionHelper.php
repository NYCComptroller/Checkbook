<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class ExceptionHelper {

    public static function getExceptionMessage(Exception $e) {
        $message = $e->getMessage();
        if (isset($message) && (strlen($message) == 0)) {
            $message = NULL;
        }

        if (!isset($message)) {
            $message = 'NO MESSAGE';
        }

        return $message;
    }
}
