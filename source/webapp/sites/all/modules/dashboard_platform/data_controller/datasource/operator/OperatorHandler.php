<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




interface OperatorHandler {

    function isSubsetBased();
}


class OperatorParameter extends AbstractObject {

    public $name = NULL;
    public $publicName = NULL;
    public $required = TRUE;
    public $defaultValue = NULL;

    public function __construct($name, $publicName = NULL, $required = TRUE, $defaultValue = NULL) {
        parent::__construct();
        $this->name = $name;
        $this->publicName = t(isset($publicName) ? $publicName : $name);
        $this->required = $required;
        $this->defaultValue = $defaultValue;
    }
}


interface OperatorMetaData {

    function getParameters();
}


interface ParameterBasedOperatorHandler {

    function getParameterDataType();
}
