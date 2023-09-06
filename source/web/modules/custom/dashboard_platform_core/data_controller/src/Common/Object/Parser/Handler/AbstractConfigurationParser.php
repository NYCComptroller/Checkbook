<?php
/**
 * This file is part of the Checkbook NYC financial transparency software.
 *
 * Copyright (c) 2012 – 2023 New York City
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Drupal\data_controller\Common\Object\Parser\Handler;

use Drupal\data_controller\Common\Object\Exception\UnsupportedOperationException;
use Drupal\data_controller\Common\Object\Parser\ConfigurationParser;
use Drupal\data_controller\Common\Object\Parser\ParserCallbackObject;
use Drupal\data_controller\Common\Pattern\AbstractObject;

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
