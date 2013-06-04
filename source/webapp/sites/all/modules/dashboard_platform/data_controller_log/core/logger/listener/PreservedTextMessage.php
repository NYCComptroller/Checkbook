<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class PreservedTextMessage extends AbstractObject {

    private $message = NULL;

    public function __construct($message) {
        parent::__construct();
        $this->message = $message;
    }

    public function __toString() {
        return isset($this->message) ? $this->message : 'N/A';
    }
}
