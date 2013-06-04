<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class LocalTransaction extends AbstractTransaction {

    public function enlistResource($resourceName, ResourceManager $resource) {
        if (count($this->resources) == 1) {
            throw new IllegalStateException(t('Only one Resource Manager can be associated with this transaction'));
        }

        parent::enlistResource($resourceName, $resource);

        $resource->start($this->xid, TMNOFLAGS);
    }

    public function commit() {



        if ($this->status == TransactionStatus::ACTIVE) {

        }
        else {

        }
    }

    public function rollback() {
        if ($this->status == TransactionStatus::ROLLEDBACK) {
            throw new IllegalStateException(t('This transaction has been rolled back already'));
        }

        if (($this->status == TransactionStatus::ACTIVE) || ($this->status == TransactionStatus::MARKED_ROLLBACK)) {
            $this->status = TransactionStatus::ROLLING_BACK;

            if (isset($this->resources)) {
                foreach ($this->resources as $resource) {
                    $resource->rollback($this->xid);
                }
            }

            $this->status = TransactionStatus::ROLLEDBACK;
        }
        else {
            throw new IllegalStateException(t(
                "Transaction is in '@status' status. It cannot be rolled back",
                array('@status' => $this->status)));
        }
    }
}
