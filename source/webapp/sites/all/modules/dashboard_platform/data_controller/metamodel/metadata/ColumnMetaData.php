<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




class ColumnMetaData extends AbstractMetaData {

    public $alias = NULL;
    /**
     * @var ColumnType
     */
    public $type = NULL;

    public $columnIndex = NULL;
    public $sourceName = NULL;

    public $key = NULL;
    public $containsUniqueValues = NULL;
    public $visible = NULL;
    public $used = NULL;

    public function __construct() {
        parent::__construct();
        $this->type = $this->initiateType();
    }

    public function __clone() {
        parent::__clone();

        $this->type = clone $this->type;
    }

    public function initializeFrom($sourceColumn) {
        parent::initializeFrom($sourceColumn);

        $sourceType = ObjectHelper::getPropertyValue($sourceColumn, 'type');
        if (isset($sourceType)) {
            $this->initializeTypeFrom($sourceType);
        }
    }

    public function initializeTypeFrom($sourceType) {
        if (isset($sourceType)) {
            ObjectHelper::mergeWith($this->type, $sourceType, TRUE);
        }
    }

    public function initiateType() {
        return new ColumnType();
    }

    public function isKey() {
        return isset($this->key) ? $this->key : FALSE;
    }

    public function isUnique() {
        return isset($this->containsUniqueValues) ? $this->containsUniqueValues : FALSE;
    }

    public function isVisible() {
        $isVisible = isset($this->visible) ? $this->visible : TRUE;
        if ($isVisible) {
            $isVisible = $this->isUsed();
        }

        return $isVisible;
    }

    public function isUsed() {
        return isset($this->used) ? $this->used : TRUE;
    }
}
