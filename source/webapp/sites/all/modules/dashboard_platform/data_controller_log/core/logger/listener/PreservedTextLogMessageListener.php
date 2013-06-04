<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class PreservedTextLogMessageListener extends AbstractLogMessageListener {

    public function log($level, &$message) {
        if ($message instanceof PreservedTextMessage) {
            $message = (string) $message;
        }
    }
}
