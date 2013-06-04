<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




abstract class AbstractDatasetSourceAssembler extends AbstractObject implements DatasetSourceAssembler {

    protected $config = NULL;

    public function __construct($config) {
        parent::__construct();
        $this->config = $config;
    }
}
