<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class MeasureMetaData extends AbstractMetaData {

    public $type = NULL;
    public $aggregationType = NULL;
    public $function = NULL;

    public function __construct() {
        parent::__construct();
        $this->type = $this->initiateType();
    }

    public function __clone() {
        parent::__clone();
        $this->type = clone $this->type;
    }

    public function initializeTypeFrom($sourceType) {
        if (isset($sourceType)) {
            ObjectHelper::mergeWith($this->type, $sourceType, TRUE);
        }
    }

    protected function initiateType() {
        return new ColumnType();
    }

    public function finalize() {
        parent::finalize();

        $parser = new EnvironmentConfigurationParser();
        $this->function = $parser->parse($this->function, array($parser, 'executeStatement'));
    }

    public function isComplete() {
        return parent::isComplete() && isset($this->type->applicationType);
    }
}
