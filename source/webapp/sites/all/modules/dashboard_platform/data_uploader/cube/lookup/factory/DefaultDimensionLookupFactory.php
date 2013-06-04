<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
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
