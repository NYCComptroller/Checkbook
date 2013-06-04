<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


abstract class AbstractPreviousOperatorHandler extends AbstractBoundaryOperatorHandler implements ParameterBasedOperatorHandler {

    public function getParameterDataType() {
        return IntegerDataTypeHandler::$DATA_TYPE;
    }
}


class PreviousOperatorMetaData extends AbstractOperatorMetaData {

    protected function initiateParameters() {
        return array(new OperatorParameter('occurenceIndex', 'Occurence Index'));
    }
}
