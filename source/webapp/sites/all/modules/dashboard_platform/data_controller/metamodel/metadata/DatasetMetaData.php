<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




class DatasetMetaData extends RecordMetaData {

    public static $STORAGE_TYPE__DATA_CONTROLLER_MANAGED = 'Data Controller Managed';

    public $aliases = NULL;

    public $datasourceName = NULL;
    public $source = NULL;
    /**
     * @var DatasetAssembler
     */
    public $assembler = NULL;
    public $storageType = NULL;

    // System datasets are created to support internal behaviour. These datasets should not be exposed to public
    public $system = NULL;

    public $shared = NULL;

    // Internal version of the dataset definition
    public $version = NULL;

    protected function getEntityName() {
        return t('Dataset');
    }

    public function isAliasMatched($alias) {
        return isset($this->aliases) && (array_search($alias, $this->aliases) !== FALSE);
    }

    public function initializeFrom($sourceDataset) {
        parent::initializeFrom($sourceDataset);

        $sourceSource = ObjectHelper::getPropertyValue($sourceDataset, 'source');
        if (isset($sourceSource)) {
            $this->initializeSourceFrom($sourceSource);
        }

        $sourceAssembler = ObjectHelper::getPropertyValue($sourceDataset, 'assembler');
        if (isset($sourceAssembler)) {
            $this->initializeAssemblerFrom($sourceAssembler);
        }
    }

    public function initializeSourceFrom($sourceSource) {
        if (isset($sourceSource)) {
            ObjectHelper::mergeWith($this->source, $sourceSource, TRUE);
        }
    }

    public function initializeAssemblerFrom($sourceAssembler) {
        if (isset($sourceAssembler)) {
            if (!isset($this->assembler)) {
                $this->assembler = $this->initiateAssembler();
            }
            ObjectHelper::mergeWith($this->assembler, $sourceAssembler, TRUE);
        }
    }

    public function initiateAssembler() {
        return new DatasetAssembler();
    }

    public function isSystem() {
        return isset($this->system) ? $this->system : FALSE;
    }

    public function isShared() {
        return isset($this->shared) ? $this->shared : FALSE;
    }
}

class DatasetAssembler extends AbstractObject {

    public $type = NULL;
    public $config = NULL;
}
