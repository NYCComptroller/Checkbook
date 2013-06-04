<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




abstract class AbstractMetaModel extends AbstractObject {

    public $version = NULL;
    private $assembled = FALSE;

    public function __construct() {
        parent::__construct();
        $this->version = uniqid();
    }

    public function isAssembled() {
        return $this->assembled;
    }

    public function startAssembling() {
        if (!$this->assembled) {
            throw new IllegalStateException(t('Meta Model assembling has already been started'));
        }

        $this->assembled = FALSE;
    }

    public function markAsAssembled() {
        $this->finalize();
        $this->validate();
        $this->assembled = TRUE;
    }

    protected function finalize() {}

    protected function validate() {}

    protected function checkAssemblingStarted() {
        if ($this->assembled) {
            LogHelper::log_error(t('Meta Model assembling has not been started'));
        }
    }
}
