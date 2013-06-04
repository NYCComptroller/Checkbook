<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




class LevelMetaData extends AbstractMetaData {

    public $sourceColumnName = NULL;
    public $datasetName = NULL;
    /**
     * @var DatasetMetaData|null
     */
    public $dataset = NULL; // populated when dataset meta data is loaded

    public function isComplete() {
        return parent::isComplete() && (!isset($this->datasetName) || (isset($this->dataset) && $this->dataset->isComplete()));
    }
}
