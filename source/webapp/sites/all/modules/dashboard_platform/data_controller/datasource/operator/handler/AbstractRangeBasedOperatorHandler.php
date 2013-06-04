<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


abstract class AbstractRangeBasedOperatorHandler extends AbstractOperatorHandler implements ParameterBasedOperatorHandler {

    public $from = NULL;
    public $to = NULL;

    public function __construct($configuration, $from = NULL, $to = NULL) {
        parent::__construct($configuration);

        $this->from = StringHelper::trim($from);
        $this->to = StringHelper::trim($to);
    }

    public function getParameterDataType() {
        return DataTypeFactory::getInstance()->autoDetectPrimaryDataType(array($this->from, $this->to));
    }
}

class RangeBasedOperatorMetaData extends AbstractOperatorMetaData {

    protected function initiateParameters() {
        return array(
            new OperatorParameter('from', 'From'),
            new OperatorParameter('to', 'To'));
    }
}
