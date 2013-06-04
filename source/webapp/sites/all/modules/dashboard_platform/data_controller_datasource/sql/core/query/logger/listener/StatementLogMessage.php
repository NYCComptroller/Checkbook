<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class StatementLogMessage extends AbstractObject {

    public $type = NULL;
    public $statement = NULL;

    public function __construct($type, $statement) {
        parent::__construct();
        $this->type = $type;
        $this->statement = $statement;
    }

    public function __toString() {
        return isset($this->statement)
            ? (is_array($this->statement) ? implode("\n", $this->statement) : $this->statement)
            : 'N/A';
    }
}
