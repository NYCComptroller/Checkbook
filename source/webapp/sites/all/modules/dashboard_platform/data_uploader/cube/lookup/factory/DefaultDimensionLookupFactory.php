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


class DefaultDimensionLookupFactory extends DimensionLookupFactory {

    private $handlerConfigurations = NULL;
    private $handlerInstances = NULL;

    public function __construct() {
        parent::__construct();
        $this->handlerConfigurations = module_invoke_all('du_star_schema_lookup');
    }

    public function registerHandlerConfiguration($datatype, $classname) {
        $this->handlerConfigurations[$datatype] = $this->prepareHandlerConfiguration($datatype, $classname);
    }

    protected function prepareHandlerConfiguration($datatype, $classname) {
        return array('classname' => $classname);
    }

    public function findHandler($datatype) {
        // checking internal cache
        if (isset($this->handlerInstances[$datatype])) {
            return $this->handlerInstances[$datatype];
        }

        // looking for configuration for the type
        $handlerConfiguration = isset($this->handlerConfigurations[$datatype]) ? $this->handlerConfigurations[$datatype] : NULL;
        if (!isset($handlerConfiguration)) {
            // checking if data type is a reference to lookup dataset column
            list($datasetName) = ReferencePathHelper::splitReference($datatype);
            if (isset($datasetName)) {
                $handlerConfiguration = $this->prepareHandlerConfiguration($datatype, 'LookupDatasetColumnDimensionLookupHandler');
            }
        }
        if (!isset($handlerConfiguration)) {
            return NULL;
        }

        // initializing handler for the type
        $classname = $handlerConfiguration['classname'];
        $handler = new $classname($datatype);
        $this->handlerInstances[$datatype] = $handler;

        return $handler;
    }

    public function getHandler($datatype) {
        $handler = $this->findHandler($datatype);
        if (!isset($handler)) {
            throw new IllegalArgumentException(t(
            	"Lookup handler is not available for '@datatype' data type",
                array('@datatype' => $datatype)));
        }

        return $handler;
    }
}
