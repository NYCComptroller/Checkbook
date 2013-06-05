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


abstract class AbstractSubsetDataSubmitter extends AbstractDataSubmitter {

    protected $skipRecordCount = NULL;
    protected $limitRecordCount = NULL;

    protected $skippedRecordCount = 0;
    protected $processedRecordCount = 0;

    public function __construct($skipRecordCount = 0, $limitRecordCount = NULL) {
        parent::__construct();
        $this->skipRecordCount = $skipRecordCount;
        $this->limitRecordCount = $limitRecordCount;
    }
}
