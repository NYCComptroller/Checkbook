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


class DefaultXIDFactory extends XIDFactory {

    public function newXID() {
        $generator = XIDGeneratorFactory::getInstance()->getGenerator();

        $formatId = $generator->getFormatId();
        if ($formatId == XID_FORMAT_NULL) {
            throw new IllegalArgumentException(t('Unsupported XID format identifier: @formatId', array('@formatId' => $formatId)));
        }

        $globalTransactionId = $generator->generateGlobalTransactionId();
        if (strlen($globalTransactionId) > MAXGTRIDSIZE) {
            throw new IllegalArgumentException(t(
                'Global transaction identifier is too long. Max length is @globalTransactionIdLength bytes',
                array('@globalTransactionIdLength' => MAXGTRIDSIZE)));
        }

        $branchQualifier = $generator->generateBranchQualifier();
        if (strlen($branchQualifier) > MAXBQUALSIZE) {
            throw new IllegalArgumentException(t(
                'Global transaction branch qualifier is too long. Max length is @branchQualifierLength bytes',
                array('@branchQualifierLength' => MAXBQUALSIZE)));
        }

        $xid = new XID();
        $xid->formatId = $formatId;
        $xid->globalTransactionId = $globalTransactionId;
        $xid->branchQualifier = $branchQualifier;

        return $xid;
    }
}
