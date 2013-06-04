<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




class Json2PHPArray extends AbstractJson2PHP {

    public function __construct($cleanInput = TRUE) {
        parent::__construct(TRUE, $cleanInput);
    }
}

class Json2PHPObject extends AbstractJson2PHP {

    public function __construct($cleanInput = TRUE) {
        parent::__construct(FALSE, $cleanInput);
    }
}
