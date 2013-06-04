<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class CreateDatabaseRequest extends AbstractDatabaseRequest {

    public $options = NULL;

    public function __construct($datasourceName, $options = NULL) {
        parent::__construct($datasourceName);
        $this->options = $options;
    }
}
