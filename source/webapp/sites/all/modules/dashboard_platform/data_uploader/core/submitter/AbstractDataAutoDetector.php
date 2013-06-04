<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


abstract class AbstractDataAutoDetector extends AbstractDataSubmitter {

    protected $minimumRecordCount = NULL;
    protected $maximumRecordCount = NULL;

    protected $processedRecordCount = 0;

    public function __construct($minimumRecordCount = NULL, $maximumRecordCount = NULL) {
        parent::__construct();
        $this->minimumRecordCount = $minimumRecordCount;
        $this->maximumRecordCount = $maximumRecordCount;
    }
}
