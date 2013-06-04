<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class DataSourceMetaData extends AbstractMetaData {

    public $parentName = NULL;
    public $type = NULL;

    public $readonly = NULL;
    public $system = NULL;

    public $shared = NULL;

    public function initializeInstanceFrom($sourceDataSource) {
        // we need to support some unknown composite properties
        ObjectHelper::mergeWith($this, $sourceDataSource, TRUE);
    }

    public function isReadOnly() {
        return isset($this->readonly) ? $this->readonly : FALSE;
    }

    public function isSystem() {
        return isset($this->system) ? $this->system : FALSE;
    }

    public function isShared() {
        return isset($this->shared) ? $this->shared : FALSE;
    }
}
