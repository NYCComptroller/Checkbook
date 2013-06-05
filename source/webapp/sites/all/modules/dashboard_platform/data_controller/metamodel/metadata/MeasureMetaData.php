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
