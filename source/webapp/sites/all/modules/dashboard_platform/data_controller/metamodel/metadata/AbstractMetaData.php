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


abstract class AbstractMetaData extends AbstractObject {

    public $name = NULL;
    public $publicName = NULL;
    public $description = NULL;

    // TRUE: the meta data created temporarily to process a particular request
    public $temporary = FALSE;
    // all meta data is prepared/calculated
    public $complete = NULL;

    // loader which loaded the meta data
    public $loader = NULL;

    public function initializeFrom($source) {
        $this->initializeInstanceFrom($source);
    }

    public function initializeInstanceFrom($source) {
        ObjectHelper::mergeWith($this, $source);
    }

    public function finalize() {
        if (!isset($this->publicName)) {
            $this->publicName = $this->name;
        }
    }

    public function isComplete() {
        return isset($this->complete) ? $this->complete : TRUE;
    }

    protected function markAsIncomplete() {
        $this->complete = FALSE;
    }

    public function markAsComplete() {
        $this->complete = TRUE;
    }
}
