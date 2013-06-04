<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


abstract class AbstractDataQueryControllerRequest extends AbstractObject {

    public $datasetName = NULL;
    public $columns = NULL;
    public $parameters = NULL;
    public $orderBy = NULL;
    public $startWith = 0;
    public $limit = NULL;
    /**
     * @var ResultFormatter
     */
    public $resultFormatter = NULL;

    public function initializeFrom($datasetName, $columns = NULL, $parameters = NULL, $orderBy = NULL, $startWith = 0, $limit = NULL, ResultFormatter $resultFormatter = NULL) {
        $this->datasetName = $datasetName;
        $this->columns = $columns;
        $this->parameters = $parameters;
        $this->orderBy = $orderBy;
        $this->startWith = $startWith;
        $this->limit = $limit;
        $this->resultFormatter = $resultFormatter;
    }
}
