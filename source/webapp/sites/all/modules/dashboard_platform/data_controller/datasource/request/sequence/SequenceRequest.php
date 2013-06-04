<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class SequenceRequest extends AbstractRequest {

    public $datasourceName;
    public $sequenceName;
    public $quantity;

    public function __construct($datasourceName, $sequenceName, $quantity = 1) {
        parent::__construct();
        $this->datasourceName = $datasourceName;
        $this->sequenceName = $sequenceName;
        $this->quantity = $quantity;
    }
}
