<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


abstract class AbstractTransaction extends AbstractObject implements Transaction {

    /**
     * @var XID
     */
    protected $xid = NULL;

    protected $status = TransactionStatus::NO_TRANSACTION;

    /**
     * @var ResourceManager[]
     */
    protected $resources = NULL;

    public function __construct(XID $xid) {
        parent::__construct();
        $this->xid = $xid;
        $this->status = TransactionStatus::ACTIVE;
    }

    public function __destruct() {
        if (($this->status == TransactionStatus::ACTIVE) || ($this->status == TransactionStatus::MARKED_ROLLBACK)) {
            $this->rollback();
        }
        parent::__destruct();
    }

    public function enlistResource($resourceName, ResourceManager $resource) {
        $this->checkStatusActive();

        if (isset($this->resources[$resourceName])) {
            throw new IllegalArgumentException(t(
                "'@resourceName' resource had been registered already",
                array('@resourceName' => $resourceName)));
        }

        $this->resources[$resourceName] = $resource;
    }

    public function findResource($resourceName) {
        $this->checkStatusActive();

        return isset($this->resources[$resourceName]) ? $this->resources[$resourceName] : NULL;
    }

    public function setRollbackOnly() {
        if ($this->status == TransactionStatus::ACTIVE) {
            $this->status = TransactionStatus::MARKED_ROLLBACK;
        }
        elseif ($this->status == TransactionStatus::MARKED_ROLLBACK) {
            // it had been marked for rollback
        }
        else {
            throw new IllegalStateException(t(
                "Transaction is in '@status' status. It cannot be market for rollback",
                array('@status' => $this->status)));
        }
    }

    protected function checkStatusActive() {
        if ($this->status != TransactionStatus::ACTIVE) {
            throw new IllegalStateException(t('This transaction is not in active status'));
        }
    }
}
