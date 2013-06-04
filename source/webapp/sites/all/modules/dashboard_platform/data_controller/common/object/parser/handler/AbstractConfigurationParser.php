<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


abstract class AbstractConfigurationParser extends AbstractObject implements ConfigurationParser {

    abstract protected function getStartDelimiter();
    abstract protected function getEndDelimiter();

    public function parse($expression, $callback) {
        $startDelimiter = $this->getStartDelimiter();
        $startDelimiterLength = strlen($startDelimiter);

        $endDelimiter = $this->getEndDelimiter();
        $endDelimiterLength = strlen($endDelimiter);

        $offset = 0;
        while (($startDelimiterIndex = strpos($expression, $startDelimiter, $offset)) !== FALSE) {
            $endDelimiterIndex = strpos($expression, $endDelimiter, $startDelimiterIndex + $startDelimiterLength);
            if ($endDelimiterIndex === FALSE) {
                throw new UnsupportedOperationException(t('Expression should contain equal number of starting and ending delimiters'));
            }

            $marker = substr($expression, $startDelimiterIndex + $startDelimiterLength, $endDelimiterIndex - $startDelimiterIndex - $startDelimiterLength);

            $callbackObject = new ParserCallbackObject();
            $callbackObject->marker = $marker;

            call_user_func_array($callback, array($callbackObject));

            $offset = $endDelimiterIndex + $endDelimiterLength;
            if ($callbackObject->markerUpdated || $callbackObject->removeDelimiters) {
                $expression = substr_replace(
                    $expression,
                    $callbackObject->marker,
                    $startDelimiterIndex + ($callbackObject->removeDelimiters ? 0 : $startDelimiterLength),
                    $endDelimiterIndex - $startDelimiterIndex - ($callbackObject->removeDelimiters ? -$endDelimiterLength : $startDelimiterLength));

                $offset += strlen($callbackObject->marker) - strlen($marker);
                if ($callbackObject->removeDelimiters) {
                    $offset -= $startDelimiterLength + $endDelimiterLength;
                }
            }
        }

        return $expression;
    }

    public function assemble($marker) {
        return $this->getStartDelimiter() . $marker . $this->getEndDelimiter();
    }
}
