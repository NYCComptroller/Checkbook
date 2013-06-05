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


abstract class AbstractDocumentGenerator extends AbstractObject {

    public $parent = NULL;

    protected $nestedGenerators = NULL;

    public function __construct(AbstractDocumentGenerator $parent = NULL) {
        parent::__construct();
        $this->parent = $parent;
    }

    public function findParentGenerator($classname) {
        return isset($this->parent)
            ? ((get_class($this->parent) == $classname) ? $this->parent : $this->parent->findParentGenerator($classname))
            : NULL;
    }

    public function getParentGenerator($classname) {
        $generator = $this->findParentGenerator($classname);
        if (!isset($generator)) {
            throw new IllegalStateException(t('Could not find parent class: @parentClassName', array('@parentClassName' => $classname)));
        }

        return $generator;
    }
    
    public function registerNestedGenerator(AbstractDocumentGenerator $generator) {
        $this->nestedGenerators[] = $generator;
    }

    protected function startTag($tagName, $cssClassName = NULL, array $tagAttributes = NULL) {
        if (isset($cssClassName)) {
            $tagAttributes['class'] = $cssClassName;
        }

        $tag = '<' . $tagName;
        if (isset($tagAttributes)) {
            foreach ($tagAttributes as $name => $value) {
                $tag .= ' ' . $name . '="' . $value . '"';
            }
        }
        $tag .= '>';

        return $tag;
    }

    protected function endTag($tagName) {
        return "</$tagName>";
    }

    protected function startGeneration(&$buffer) {}
    protected function finishGeneration(&$buffer) {}

    protected function startNestedGeneration(&$buffer) {}
    protected function finishNestedGeneration(&$buffer) {}
    protected function startNestedItemGeneration(&$buffer) {}
    protected function finishNestedItemGeneration(&$buffer) {}

    public function generate(&$buffer) {
        $this->startGeneration($buffer);

        if (isset($this->nestedGenerators)) {
            $this->startNestedGeneration($buffer);
            if (is_array($this->nestedGenerators)) {
                foreach ($this->nestedGenerators as $generator) {
                    $this->startNestedItemGeneration($buffer);
                    $generator->generate($buffer);
                    $this->finishNestedItemGeneration($buffer);
                }
            }
            else {
                $this->nestedGenerators->generate($buffer);
            }
            $this->finishNestedGeneration($buffer);
        }

        $this->finishGeneration($buffer);
    }
}
