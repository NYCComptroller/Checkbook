<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


abstract class AbstractMetaData extends AbstractObject {

    public $name = NULL;
    public $publicName = NULL;
    public $description = NULL;

    // TRUE: the meta data created temporarily to process a particular request
    public $temporary = FALSE;
    // all meta data is prepared/calculated
    public $complete = NULL;

    // loader which loaded the meta data
    public $loader = NULL;

    public function initializeFrom($source) {
        $this->initializeInstanceFrom($source);
    }

    public function initializeInstanceFrom($source) {
        ObjectHelper::mergeWith($this, $source);
    }

    public function finalize() {
        if (!isset($this->publicName)) {
            $this->publicName = $this->name;
        }
    }

    public function isComplete() {
        return isset($this->complete) ? $this->complete : TRUE;
    }

    protected function markAsIncomplete() {
        $this->complete = FALSE;
    }

    public function markAsComplete() {
        $this->complete = TRUE;
    }
}
