<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class TransactionSupporter extends AbstractDataSubmitter {

    private $datasourceName = NULL;

    public function __construct($datasourceName) {
        parent::__construct();
        $this->datasourceName = $datasourceName;
    }

    public function start() {
        TransactionManager::getInstance()->startTransaction($this->datasourceName);

        return TRUE;
    }

    public function finish() {
        $transaction = TransactionManager::getInstance()->getTransaction($this->datasourceName);
        $transaction->commit();
    }

    public function abort() {
        $transaction = TransactionManager::getInstance()->getTransaction($this->datasourceName);
        $transaction->rollback();
    }
}
