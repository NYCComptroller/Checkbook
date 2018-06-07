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


abstract class AbstractDataQueryControllerRequest extends AbstractObject {

    public $datasetName = NULL;
    public $columns = NULL;
    public $parameters = NULL;
    public $orderBy = NULL;
    public $startWith = 0;
    public $limit = NULL;
    public $logicalOrColumns = NULL;
    public $sortSourceByNull=NULL;
    /**
     * @var ResultFormatter
     */
    public $resultFormatter = NULL;

    public function initializeFrom($datasetName, $columns = NULL, $parameters = NULL, $orderBy = NULL, $startWith = 0, $limit = NULL, ResultFormatter $resultFormatter = NULL) {
        if(isset($parameters)) {
            foreach($parameters as $name => $value) {
                if($name == "logicalOrColumns") {
                    $this->logicalOrColumns = $value;
                    unset($parameters[$name]);
                }

               elseif ($name=="sortSourceByNull"){
                    $this->sortSourceByNull=$value;
                    unset($parameters[$name]);
                }
            }

        }
        $this->datasetName = $datasetName;
        $this->columns = $columns;
        $this->parameters = $parameters;
        $this->orderBy = $orderBy;
        $this->startWith = $startWith;
        $this->limit = $limit;
        $this->resultFormatter = $resultFormatter;
    }
}
