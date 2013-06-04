<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class ResourceDocumentGenerator extends AbstractDocumentGenerator {

    public static $CSS_CLASS__RESOURCE = 'dpc_resource';
    public static $CSS_CLASS__RESOURCE_NAME = 'dpc_resource_name';

    public $resourceName = NULL;

    public function __construct(AbstractDocumentGenerator $parent, $resourceName) {
        parent::__construct($parent);
        $this->resourceName = $resourceName;
    }

    protected function startGeneration(&$buffer) {
        $buffer .= self::startTag('div', self::$CSS_CLASS__RESOURCE);

        $buffer .= self::startTag('h3', self::$CSS_CLASS__RESOURCE_NAME);
        $buffer .= $this->resourceName;
        $buffer .= self::endTag('h3');
    }

    protected function finishGeneration(&$buffer) {
        $buffer .= self::endTag('div');
    }
}
