<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




abstract class AbstractDatasetManipulationRequest extends AbstractRequest {

    public $datasetName = NULL;
    /**
     * @var AbstractRecordsHolder
     */
    public $recordsHolder = NULL;

    public function __construct($datasetName, AbstractRecordsHolder $recordsHolder) {
        parent::__construct();
        $this->datasetName = $datasetName;
        $this->recordsHolder = $recordsHolder;
    }

    abstract public function getOperationName();
}
