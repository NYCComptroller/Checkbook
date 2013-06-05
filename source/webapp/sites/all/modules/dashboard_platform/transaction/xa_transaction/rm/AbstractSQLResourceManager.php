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
