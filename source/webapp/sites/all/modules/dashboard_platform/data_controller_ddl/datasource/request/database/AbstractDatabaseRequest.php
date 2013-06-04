<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


abstract class AbstractDatabaseRequest extends AbstractRequest {

    public $datasourceName = NULL;

    public function __construct($datasourceName) {
        parent::__construct();
        $this->datasourceName = $datasourceName;
    }
}
