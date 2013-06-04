<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


interface TransactionStatus {

    const UNKNOWN         = 'Unknown';
    const NO_TRANSACTION  = 'No Transaction';
    const ACTIVE          = 'Active';
    const MARKED_ROLLBACK = 'Marked for Rollback';
    const PREPARING       = 'Preparation in Progress';
    const PREPARED        = 'Prepared';
    const COMMITTING      = 'Commit in Progress';
    const COMMITTED       = 'Committed';
    const ROLLING_BACK    = 'Rollback in Progress';
    const ROLLEDBACK      = 'Rolled back';
}


interface Transaction {

    function enlistResource($resourceName, ResourceManager $resource);
    function findResource($resourceName);

    function setRollbackOnly();

    function commit();
    function rollback();
}
