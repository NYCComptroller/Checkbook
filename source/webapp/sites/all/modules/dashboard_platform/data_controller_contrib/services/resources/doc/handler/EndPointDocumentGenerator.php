<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class EndPointDocumentGenerator extends AbstractDocumentGenerator {

    public static $CSS_CLASS__ENDPOINT = 'dpc_endpoint';

    public $endpointDef = NULL;

    public function __construct(array $endpointDef) {
        parent::__construct(NULL);
        $this->endpointDef = $endpointDef;
    }

    protected function startGeneration(&$buffer) {
        $buffer .= self::startTag('div', self::$CSS_CLASS__ENDPOINT);
    }

    protected function finishGeneration(&$buffer) {
        $buffer .= self::endTag('div');
    }
}
