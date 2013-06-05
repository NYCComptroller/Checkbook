<?php
/**
* This file is part of the Checkbook NYC financial transparency software.
* 
* Copyright (C) 2012, 2013 New York City
* 
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU Affero General Public License as
* published by the Free Software Foundation, either version 3 of the
* License, or (at your option) any later version.
* 
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU Affero General Public License for more details.
* 
* You should have received a copy of the GNU Affero General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
