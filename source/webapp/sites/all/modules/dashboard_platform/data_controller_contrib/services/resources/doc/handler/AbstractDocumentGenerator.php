<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
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
