<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


/*
* xa_() return codes (resource manager reports to transaction manager)
*/
define('XA_RBBASE',      100);            // the inclusive lower bound of the rollback codes
define('XA_RBROLLBACK',  XA_RBBASE);      // the rollback was caused by an unspecified reason
define('XA_RBCOMMFAIL',  XA_RBBASE + 1);  // the rollback was caused by a communication failure
define('XA_RBDEADLOCK',  XA_RBBASE + 2);  // a deadlock was detected
define('XA_RBINTEGRITY', XA_RBBASE + 3);  // a condition that violates the integrity of the resources was detected
define('XA_RBOTHER',     XA_RBBASE + 4);  // the resource manager rolled back the transaction branch for a reason not on this list
define('XA_RBPROTO',     XA_RBBASE + 5);  // a protocol error occurred in the resource manager
define('XA_RBTIMEOUT',   XA_RBBASE + 6);  // a transaction branch took too long
define('XA_RBTRANSIENT', XA_RBBASE + 7);  // may retry the transaction branch
define('XA_RBEND',       XA_RBTRANSIENT); // the inclusive upper bound of the rollback codes
define('XA_NOMIGRATE',   9);              // resumption must occur where suspension occurred
define('XA_HEURHAZ',     8);              // the transaction branch may have been heuristically completed
define('XA_HEURCOM',     7);              // the transaction branch has been heuristically committed
define('XA_HEURRB',      6);              // the transaction branch has been heuristically rolled back
define('XA_HEURMIX',     5);              // the transaction branch has been heuristically committed and rolled back
define('XA_RETRY',       4);              // routine returned with no effect and may be reissued
define('XA_RDONLY',      3);              // the transaction branch was read-only and has been committed
define('XA_OK',          0);              // normal execution
define('XAER_ASYNC',    -2);              // asynchronous operation already outstanding
define('XAER_RMERR',    -3);              // a resource manager error occurred in the transaction branch
define('XAER_NOTA',     -4);              // the XID is not valid
define('XAER_INVAL',    -5);              // invalid arguments were given
define('XAER_PROTO',    -6);              // routine invoked in an improper context
define('XAER_RMFAIL',   -7);              // resource manager unavailable
define('XAER_DUPID',    -8);              // the XID already exists
define('XAER_OUTSIDE',  -9);              // resource manager doing work outside global transaction


interface ResourceManager {

    /*
     * page 55[42]
     * open a resource manager
     */
    function xa_open($xa_info, $flags);

    /*
     * page 43[30]
     * close a resource manager
     */
    function xa_close($xa_info, $flags);

    /*
     * page 65[52]
     * start work on behalf of a transaction branch
     */
    function start($xid, $flags);
    /*
     * page 50[37]
     * end work performed on behalf of a transaction branch
     */
    function end($xid, $flags);

    /*
     * page 57[44]
     * prepare to commit work done on behalf of a transaction branch
     */
    function prepare($xid);
    /*
     * page 45[32]
     * commit work done on behalf of a transaction branch
     */
    function commit($xid, $onePhase);
    /*
     * page 62[49]
     * roll back work done on behalf of a transaction branch
     */
    function rollback($xid);

    /*
     * page 53[40]
     * forget about a heuristically completed transaction branch
     */
    function forget($xid);

    /*
     * page 48[35]
     * wait for an asynchronous operation to complete
     */
    function xa_complete($handle, $retval, $flags);

    /**
     * page 60[47]
     * obtain a list of prepared transaction branches from a resource manager
     *
     * @param $flags
     * @return XID[]
     */
    function recover($flags);
}
