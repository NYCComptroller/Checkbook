<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


abstract class AbstractWildcardOperatorHandler extends AbstractOperatorHandler implements ParameterBasedOperatorHandler {

    public $wildcard = NULL;
    public $anyCharactersOnLeft = FALSE;
    public $anyCharactersOnRight = FALSE;

    public function __construct($configuration, $wildcard, $anyCharactersOnLeft = FALSE, $anyCharactersOnRight = FALSE) {
        parent::__construct($configuration);

        $this->wildcard = StringHelper::trim($wildcard);
        $this->anyCharactersOnLeft = $anyCharactersOnLeft;
        $this->anyCharactersOnRight = $anyCharactersOnRight;
    }

    public function getParameterDataType() {
        return StringDataTypeHandler::$DATA_TYPE;
    }
}


class WildcardOperatorMetaData extends AbstractOperatorMetaData {

    protected function initiateParameters() {
        return array(
            new OperatorParameter('wildcard', 'Wildcard'),
            new OperatorParameter('anyCharactersOnLeft', 'Any Characters on Left', FALSE, FALSE),
            new OperatorParameter('anyCharactersOnRight', 'Any Characters on Right', FALSE, FALSE));
    }
}
