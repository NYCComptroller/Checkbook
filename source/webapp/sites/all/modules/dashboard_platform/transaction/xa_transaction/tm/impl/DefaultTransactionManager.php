<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class DefaultTransactionManager extends TransactionManager {

    /**
     * @return Transaction
     */
    protected function findTransaction() {
        return XATransaction::findCurrentTransaction();
    }

    public function getTransaction() {
        $transaction = $this->findTransaction();
        if (isset($transaction)) {
            return $transaction;
        }

        $localXID = new XID();

        return new LocalTransaction($localXID);
    }

    public function startTransaction() {
        $this->checkTransactionNotStartedYet();

        $xid = XIDFactory::getInstance()->newXID();

        return new XATransaction($xid);
    }

    protected function checkTransactionNotStartedYet() {
        if ($this->findTransaction() != NULL) {
            throw new IllegalStateException(t('A transaction has already been started'));
        }
    }
}

