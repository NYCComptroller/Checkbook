<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


abstract class AbstractRegularExpressionOperatorHandler extends AbstractOperatorHandler implements ParameterBasedOperatorHandler {

    public $pattern = NULL;

    public function __construct($configuration, $pattern) {
        parent::__construct($configuration);

        $this->pattern = StringHelper::trim($pattern);
    }

    public function getParameterDataType() {
        return StringDataTypeHandler::$DATA_TYPE;
    }
}

class RegularExpressionOperatorMetaData extends AbstractOperatorMetaData {

    protected function initiateParameters() {
        return array(new OperatorParameter('pattern', 'Pattern'));
    }
}
