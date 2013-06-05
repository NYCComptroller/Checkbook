<?php
/**
* This file is part of the Checkbook NYC financial transparency software.
* 
* Copyright (C) 2012, 2013 New York City
* 
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU Affero General Public License as
* published by the Free Software Foundation, either version 3 of the
* License, or (at your option) any later version.
* 
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU Affero General Public License for more details.
* 
* You should have received a copy of the GNU Affero General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
