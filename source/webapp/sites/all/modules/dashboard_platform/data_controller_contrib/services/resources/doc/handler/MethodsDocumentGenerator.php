<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class MethodsDocumentGenerator extends AbstractDocumentGenerator {

    public static $CSS_CLASS__METHODS = 'dpc_methods';

    protected function startGeneration(&$buffer) {
        $buffer .= self::startTag('div', self::$CSS_CLASS__METHODS);
    }

    protected function finishGeneration(&$buffer) {
        $buffer .= self::endTag('div');
    }

    protected function startNestedGeneration(&$buffer) {
        $buffer .= self::startTag('table', self::$CSS_CLASS__METHODS);

        $buffer .= self::startTag('tr');
        $buffer .= self::startTag('th') . 'URI' . self::endTag('th');
        $buffer .= self::startTag('th') . 'Body' . self::endTag('th');
        $buffer .= self::startTag('th') . 'Description' . self::endTag('th');
        $buffer .= self::endTag('tr');
    }

    protected function finishNestedGeneration(&$buffer) {
        $buffer .= self::endTag('table');
    }
}
