<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class DefaultConfigurationParser extends AbstractConfigurationParser {

    protected $startDelimiter = NULL;
    protected $endDelimiter = NULL;

    public function __construct($startDelimiter, $endDelimiter) {
        parent::__construct();
        $this->startDelimiter = $startDelimiter;
        $this->endDelimiter = $endDelimiter;
    }

    protected function getStartDelimiter() {
        return $this->startDelimiter;
    }

    protected function getEndDelimiter() {
        return $this->endDelimiter;
    }
}
