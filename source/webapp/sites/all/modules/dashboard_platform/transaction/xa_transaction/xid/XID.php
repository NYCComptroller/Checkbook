<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


define('MAXGTRIDSIZE', 64); // maximum size in bytes of global transaction identifier (gtrid)
define('MAXBQUALSIZE', 64); // maximum size in bytes of branch qualifier (bqual)

define('XID_FORMAT_NULL',   -1);

/*
 * Transaction branch identifier (Section 4.2)
 */
class XID extends AbstractObject {

    // check XID_FORMAT_* variables
    // > 0: other (custom) naming format
    public $formatId = XID_FORMAT_NULL;

    public $globalTransactionId = NULL;
    public $branchQualifier = NULL;

    public function isNull() {
        return $this->formatId == XID_FORMAT_NULL;
    }

    public function equals(XID $xid) {
        return (isset($xid))
            && ($this->formatId == $xid->formatId)
            && ($this->globalTransactionId == $xid->globalTransactionId)
            && ($this->branchQualifier == $xid->branchQualifier);
    }
}
