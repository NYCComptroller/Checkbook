<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


abstract class AbstractSQLResourceManager extends AbstractResourceManager {

    protected function formatXID(XID $xid) {
        return "'$xid->globalTransactionId','$xid->branchQualifier',$xid->formatId";
    }

    protected function getStartTransactionQuery($xid) {
        $formattedXID = $this->formatXID($xid);

        return "XA START $formattedXID";
    }

    protected function getEndTransactionQuery($xid) {
        $formattedXID = $this->formatXID($xid);

        return "XA END $formattedXID";
    }

    protected function getPrepareTransactionQuery($xid) {
        $formattedXID = $this->formatXID($xid);

        return "XA PREPARE $formattedXID";
    }

    protected function getCommitTransactionQuery($xid, $flags) {
        $formattedXID = $this->formatXID($xid);

        $sql = "XA COMMIT $formattedXID";
        if (($flags & TMONEPHASE) == TMONEPHASE) {
            $sql .= ' ONE PHASE';
        }

        return $sql;
    }

    protected function getRollbackTransactionQuery($xid) {
        $formattedXID = $this->formatXID($xid);

        return "XA ROLLBACK $formattedXID";
    }

    protected function getRecoverTransactionQuery($xid) {
        $formattedXID = $this->formatXID($xid);

        return "XA RECOVER $formattedXID";
    }
}
