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


abstract class AbstractResourceManager extends AbstractObject implements ResourceManager {

    private static $xa_infos = NULL;
    private $xa_info = NULL;

    private $rmid = NULL;
    /**
     * @var ResourceTransactionContext
     */
    private $transactionContext = NULL;

    public function __destruct() {
        // FIXME rollback all unprepared transactions
        // prepared transactions can be recovered later
        parent::__destruct();
    }

    protected function isLocalTransactionStarted() {
        return isset($this->transactionContext);
    }

    public function xa_open($xa_info, $rmid, $flags) {
        // page 55[42]
        // if this resource manager is already open it is ok
        // OUR RULE: we just check that rmid is the same
        if (isset($this->rmid)) {
            $this->checkRMID($rmid);
        }

        // page 55[42]
        $this->checkXAInfo($xa_info, FALSE);

        $this->rmid = $rmid;

        $this->registerXAInfo($xa_info);

        return XA_OK;
    }

    public function xa_close($xa_info, $rmid, $flags) {
        if (isset($this->rmid)) {
            $this->checkRMID($rmid);

            $this->checkXAInfo($xa_info, TRUE);

            // transaction should not be in progress [XAER_PROTO]
            $this->checkCompletedTransactionBranch();

            $this->unregisterXAInfo($xa_info);

            $this->rmid = NULL;
        }

        return XA_OK;
    }

    protected function checkXAInfo($xa_info, $shouldExists) {
        // checking that xa_info is provided
        if (!isset($xa_info)) {
            throw new IllegalArgumentException(t('Resource manager instance-specific information has not been provided'));
        }

        // checking that xa_info is unique across all resource managers
        $foundXAInfo = isset(self::$xa_infos) && (array_search($xa_info, self::$xa_infos[]) !== FALSE);
        if ($foundXAInfo != $shouldExists) {
            if ($shouldExists) {
                throw new IllegalArgumentException(t(
                    'Could not find resource domain: @xa_info',
                    array('@xa_info' => $xa_info)));
            }
            else {
                throw new IllegalArgumentException(t(
                    'Duplicate resource domain: @xa_info',
                    array('@xa_info' => $xa_info)));
            }
        }
    }

    protected function registerXAInfo($xa_info) {
        $this->xa_info = $xa_info;

        // adding the xa_info to global list
        self::$xa_infos[] = $xa_info;
    }

    protected function unregisterXAInfo($xa_info) {
        $this->xa_info = NULL;

        // removing the xa_info from global list
        $index = array_search($xa_info, self::$xa_infos[]);
        unset(self::$xa_infos[$index]);
        if (count(self::$xa_infos[]) == 0) {
            self::$xa_infos = NULL;
        }
    }

    public function xa_start($xid, $rmid, $flags) {
        throw new UnsupportedOperationException();
    }

    // FIXME call the function
    protected function joinTransactionBranch($xid) {
        $this->transactionContext = new ResourceTransactionContext();
        $this->transactionContext->xid = clone $xid;
    }

    public function xa_end($xid, $rmid, $flags) {
        throw new UnsupportedOperationException();
    }

    // FIXME call the function
    protected function markResourceUpdated() {
        $this->checkAssociationWithTransactionBranch();

        $this->transactionContext->resourceUpdated = TRUE;
    }

    public function xa_prepare($xid, $rmid, $flags) {
        $this->checkRMID($rmid);

        $this->checkAssociationWithTransactionBranch();

        if ($this->transactionContext->prepared) {
            // equivalent of XAER_PROTO
            throw new IllegalStateException(t('The transaction has been prepared already'));
        }

        if (!$this->transactionContext->resourceUpdated) {
            return XA_RDONLY;
        }

        if ($this->transactionContext->rollbackOnly) {
            // FIXME support rollback

            return XA_RBROLLBACK;
        }

        $this->transactionContext->prepared = TRUE;

        return XA_OK;
    }

    public function xa_commit($xid, $rmid, $flags) {
        $this->checkRMID($rmid);

        $this->checkAssociationWithTransactionBranch();

        if ($this->transactionContext->rollbackOnly) {
            if (($flags & TMONEPHASE) == TMONEPHASE) {
                // FIXME support rollback

                return XA_RBROLLBACK;
            }
            else {
                throw new IllegalStateException(t('A transaction marked as rollback only cannot be committed'));
            }
        }

        $this->transactionContext = NULL;

        return XA_OK;
    }

    public function xa_rollback($xid, $rmid, $flags) {
        $this->checkRMID($rmid);

        $this->checkAssociationWithTransactionBranch();

        $this->transactionContext = NULL;

        return XA_OK;
    }

    public function setRollbackOnly() {
        $this->checkAssociationWithTransactionBranch();

        $this->transactionContext->rollbackOnly = TRUE;
    }

    public function xa_forget($xid, $rmid, $flags) {
        throw new UnsupportedOperationException();
    }

    public function xa_complete($handle, $retval, $rmid, $flags) {
        throw new UnsupportedOperationException();
    }

    public function xa_recover(array $xids = NULL, $rmid, $flags) {
        $this->checkRMID($rmid);

        // FIXME support recovering of prepared transactions
    }

    protected function checkRMID($rmid) {
        if ($rmid != $this->rmid) {
            throw new IllegalStateException(t(
                'Inconsistent resource manager identifier: [current: @currentRMID; passed: @passedRMID]',
                array('@currentRMID' => $this->rmid, '@passedRMID' => $rmid)));
        }
    }

    protected function checkAssociationWithTransactionBranch() {
        if (!$this->isLocalTransactionStarted()) {
            throw new IllegalStateException(t(
                'Transaction has not been started: [rmid: @rmid]',
                array('@rmid' => $this->rmid)));
        }
    }

    protected function checkXID($xid) {
        if (!$this->transactionContext->xid->equals($xid)) {
            throw new IllegalStateException(t(
                'Invalid transaction context: [rmid: @rmid]',
                array('@rmid' => $this->rmid)));
        }
    }

    protected function checkCompletedTransactionBranch() {
        if ($this->isLocalTransactionStarted()) {
            throw new IllegalStateException(t(
                'Invalid transaction context: [rmid: @rmid]',
                array('@rmid' => $this->rmid)));
        }
    }
}

class ResourceTransactionContext extends TransactionContext {

    public $resourceUpdated = FALSE;
}
