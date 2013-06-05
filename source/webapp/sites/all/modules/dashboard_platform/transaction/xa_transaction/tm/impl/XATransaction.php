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


/*
 * ax_() return codes (transaction manager reports to resource manager)
 */
define('TM_JOIN',     2); // caller is joining existing transaction branch
define('TM_RESUME',   1); // caller is resuming association with suspended transaction branch
define('TM_OK',       0); // normal execution
define('TMER_TMERR', -1); // an error occurred in the transaction manager
define('TMER_INVAL', -2); // invalid arguments were given
define('TMER_PROTO', -3); // routine invoked in an improper context


/*
 * Flag definitions for xa_ and ax_ routines
 */
define('TMASYNC',      0x80000000); // perform routine asynchronously
define('TMONEPHASE',   0x40000000); // caller is using one-phase commit optimisation
define('TMFAIL',       0x20000000); // dissociates caller and marks transaction branch rollback-only
define('TMNOWAIT',     0x10000000); // return if blocking condition exists
define('TMRESUME',     0x08000000); // caller is resuming association with suspended transaction branch
define('TMSUCCESS',    0x04000000); // dissociate caller from transaction branch
define('TMSUSPEND',    0x02000000); // caller is suspending, not ending, association
define('TMSTARTRSCAN', 0x01000000); // start a recovery scan
define('TMENDRSCAN',   0x00800000); // end a recovery scan
define('TMMULTIPLE',   0x00400000); // wait for any asynchronous operation
define('TMJOIN',       0x00200000); // caller is joining existing transaction branch
define('TMMIGRATE',    0x00100000); // caller intends to perform migration


class XATransaction extends AbstractTransaction {

    /**
     * @var Transaction
     */
    private static $transaction = NULL;

    public static function findCurrentTransaction() {
        return self::$transaction;
    }



    public function enlistResourceManager(ResourceManager $resourceManager) {
        // TODO
        /* resourceManager->xa_open($rmp->xa_info, $rmid, TMNOFLAGS);*/
    }

    // FIXME call the function
    public function ax_reg($rmid, &$xid, $flags = TMNOFLAGS) {
        // (page 40[27])
        if ($flags != TMNOFLAGS) {
            throw new UnsupportedOperationException();
        }

        // (page 29[16]) new dynamic registration is now allowed until another PM completes its work
        $this->checkLocalTransactionNotStarted();

        $rmp = $this->getResourceManagerProxy($rmid);

        // (page 39[26]) checking that the RM is allowed to register dynamically
        $switch = $this->getSwitch($rmp->switchId);
        if (($switch->flags & TMREGISTER) != TMREGISTER) {
            throw new IllegalArgumentException(t(
                "'@ResourceManagerName' resource manager is not allowed to register dynamically",
                array('@ResourceManagerName' => $switch->name)));
        }

        if ($this->isXATransactionStarted()) {
            // joining transaction
            $xid = $this->transactionContext->xid;
        }
        else {
            // transaction has not been started.
            // It means that the RM requires work outside of a global transaction
            // Until the work is completed new xa transactions will not be allowed to start
            // and other RM will not be able to register
            if (!$this->localTransactionAllowed) {
                throw new IllegalArgumentException(t('Local transactions are not allowed'));
            }

            $xid = $rmp->startLocalTransaction();
        }

        return TM_OK;
    }

    public function ax_unreg($rmid, $flags) {
        // (page 42[29])
        if ($flags != TMNOFLAGS) {
            throw new UnsupportedOperationException();
        }

        $rmp = $this->getResourceManagerProxy($rmid);

        // (page 42[29]) checking that the RM is allowed to unregister dynamically
        $switch = $this->getSwitch($rmp->switchId);

        if (($switch->flags & TMREGISTER) != TMREGISTER) {
            throw new IllegalArgumentException(t(
                "'@ResourceManagerName' resource manager is not allowed to unregister dynamically",
                array('@ResourceManagerName' => $switch->name)));
        }

        if (!$rmp->isLocalTransactionStarted()) {
            throw new IllegalArgumentException(t(
                "Local transaction had not been started by '@ResourceManagerName' resource manager",
                array('@ResourceManagerName' => $switch->name)));
        }

        $rmp->markLocalTransactionAsCompleted();

        return TM_OK;
    }

    public function getStatus() {
        return $this->status;
    }

    public function setRollbackOnly() {
        if ($this->status != TransactionStatus::ACTIVE) {
            throw new IllegalStateException(t('This transaction has to be active to allow marking as rolled back'));
        }

        $this->status = TransactionStatus::MARKED_ROLLBACK;
    }

    public function commit() {

    }

    public function rollback() {

    }


    /**
     * @var ResourceManagerProxy[]
     */
    private $resourceManagerProxies = NULL;

    /**
     * @param $rmid
     * @return ResourceManagerProxy
     */
    protected function findResourceManagerProxy($rmid) {
        return isset($this->resourceManagerProxies[$rmid]) ? $this->resourceManagerProxies[$rmid] : NULL;
    }

    /**
     * @param $rmid
     * @return ResourceManagerProxy
     */
    protected function getResourceManagerProxy($rmid) {
        $rmp = $this->findResourceManagerProxy($rmid);
        if (!isset($rmp)) {
            throw new IllegalArgumentException(t('Unregistered resource manager instance: @rmid', array('@rmid' => $rmid)));
        }

        return $rmp;
    }

    /**
     * @param $switchId
     * @return XA_Switch
     */
    protected function findSwitch($switchId) {
        return isset($this->switches[$switchId]) ? $this->switches[$switchId] : NULL;
    }

    /**
     * @param $switchId
     * @return XA_Switch
     */
    protected function getSwitch($switchId) {
        $switch = $this->findSwitch($switchId);
        if (!isset($switch)) {
            throw new IllegalArgumentException(t('Unregistered resource manager switch: @switchId', array('@switchId' => $switchId)));
        }

        return $switch;
    }

    protected function checkLocalTransactionNotStarted() {
        if (isset($this->resourceManagerProxies)) {
            foreach ($this->resourceManagerProxies as $rmp) {
                if ($rmp->isLocalTransactionStarted()) {
                    $switch = $this->getSwitch($rmp->switchId);
                    throw new IllegalStateException(t(
                        "'@ResourceManagerName' resource manager had started local transaction",
                        array('@ResourceManagerName' => $switch->name)));
                }
            }
        }
    }

    protected function prepare() {
        $commitAllowed = TRUE;

        foreach ($this->resourceManagerProxies as $rmp) {
            try {
                $response = $rmp->resourceManager->xa_prepare($this->transactionContext->xid, $rmp->rmid, TMNOFLAGS);
            }
            catch (Exception $e) {
                $response = XAER_RMERR;

                $commitAllowed = FALSE;
                watchdog_exception(DISTRIBUTED_TRANSACTION_MODULE_NAME, $e);
            }

            if ($commitAllowed) {
                if ($response == XA_OK) {
                    // preparation was completed successfully
                }
                elseif ($response == XA_RDONLY) {
                    $this->unregisterResourceManagerProxy($rmp->rmid);
                }
                elseif (($response >= XA_RBBASE) && (XA_RBBASE <= XA_RBEND)) {
                    $commitAllowed = FALSE;
                }
                else {
                    throw new UnsupportedOperationException(t(
                        'Unsupported transaction preparation response code: @responseCode',
                        array('@responseCode' => $response)));
                }
            }
        }

        $this->transactionContext->prepared = TRUE;
        if (!$commitAllowed) {
            $this->transactionContext->rollbackOnly = FALSE;
        }

        return $commitAllowed;
    }

    public function completeTransaction() {
        $this->checkXATransactionStarted();

        // prepare resources if necessary
        $commitFlags = TMNOFLAGS;
        if (count($this->resourceManagerProxies) == 1) {
            // there is no need to use Two-Phase commit for just one resource manager
            if ($this->transactionContext->rollbackOnly) {
                $commitAllowed = FALSE;
            }
            else {
                // one-phase commit is possible
                $commitFlags += TMONEPHASE;

                $commitAllowed = TRUE;
            }
        }
        else {
            $commitAllowed = $this->prepare();
        }

        // commit resources
        // We do not try to catch any exceptions. If commit/rollback is interrupted we will complete the opertaion during 'recovery' stage
        foreach ($this->resourceManagerProxies as $rmp) {
            if ($commitAllowed) {
                $response = $rmp->resourceManager->xa_commit($this->transactionContext->xid, $rmp->rmid, $commitFlags);
                if ($response == XA_OK) {
                    // commit was completed successfully
                }
                elseif (($response >= XA_RBBASE) && (XA_RBBASE <= XA_RBEND)) {
                    // the transaction was rolled back. This could happen only when TMONEPHASE is used
                }
                else {
                    throw new UnsupportedOperationException(t(
                        'Unsupported transaction commit response code: @responseCode',
                        array('@responseCode' => $response)));
                }
            }
            else {
                $response = $rmp->resourceManager->xa_rollback($this->transactionContext->xid, $rmp->rmid, TMNOFLAGS);
                if ($response == XA_OK) {
                    // rollback was completed successfully
                }
                else {
                    throw new UnsupportedOperationException(t(
                        'Unsupported transaction rollback response code: @responseCode',
                        array('@responseCode' => $response)));
                }
            }
        }

        // releasing transaction details
        $this->transactionContext = NULL;
    }

}


class ResourceManagerProxy extends AbstractObject {

    public $switchId = NULL;
    public $rmid = NULL;

    public $xa_info = NULL;
    /**
     * @var ResourceManager
     */
    public $resourceManager = NULL;

    /**
     * @var XID
     */
    private $localXID = NULL;

    public function __construct($switchId, $rmid) {
        parent::__construct();
        $this->switchId = $switchId;
        $this->rmid = $rmid;
    }

    public function startLocalTransaction() {
        $this->localXID = new XID();

        return $this->localXID;
    }

    public function isLocalTransactionStarted() {
        return isset($this->localXID);
    }

    public function markLocalTransactionAsCompleted() {
        $this->localXID = NULL;
    }
}

interface ResourceManagerCoordinator {

    /*
     * page 39[26]
     * dynamically register a resource manager with a transaction manager
     */
    function ax_reg($rmid, &$xid, $flags);
    /*
     * page 42[29]
     * dynamically unregister a resource manager with a transaction manager
     */
    function ax_unreg($rmid, $flags);
}


define('RMNAMESZ',     32); // length of resource manager name
define('MAXINFOSIZE', 256); // maximum size in bytes of xa_info strings

/*
 * Flag definitions for the RM switch
 */
define('TMNOFLAGS',   0x00000000); // no resource manager features selected
define('TMREGISTER',  0x00000001); // resource manager dynamically registers
define('TMNOMIGRATE', 0x00000002); // resource manager does not support association migration
define('TMUSEASYNC',  0x00000004); // resource manager supports asynchronous operations

/*
 * Resource Manager Switch (Section 4.3)
 */
class XA_Switch extends AbstractObject {

    // name of resource manager
    public $name;
    // options specific to the resource manager
    public $flags;
    // must be 0
    public $version;
}
