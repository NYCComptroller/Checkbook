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
