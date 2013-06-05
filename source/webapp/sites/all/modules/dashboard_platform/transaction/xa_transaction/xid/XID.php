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
