<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


abstract class AbstractSubsetDataSubmitter extends AbstractDataSubmitter {

    protected $skipRecordCount = NULL;
    protected $limitRecordCount = NULL;

    protected $skippedRecordCount = 0;
    protected $processedRecordCount = 0;

    public function __construct($skipRecordCount = 0, $limitRecordCount = NULL) {
        parent::__construct();
        $this->skipRecordCount = $skipRecordCount;
        $this->limitRecordCount = $limitRecordCount;
    }
}
