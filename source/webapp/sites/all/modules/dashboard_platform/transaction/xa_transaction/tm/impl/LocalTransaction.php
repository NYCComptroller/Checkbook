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


class LocalTransaction extends AbstractTransaction {

    public function enlistResource($resourceName, ResourceManager $resource) {
        if (count($this->resources) == 1) {
            throw new IllegalStateException(t('Only one Resource Manager can be associated with this transaction'));
        }

        parent::enlistResource($resourceName, $resource);

        $resource->start($this->xid, TMNOFLAGS);
    }

    public function commit() {



        if ($this->status == TransactionStatus::ACTIVE) {

        }
        else {

        }
    }

    public function rollback() {
        if ($this->status == TransactionStatus::ROLLEDBACK) {
            throw new IllegalStateException(t('This transaction has been rolled back already'));
        }

        if (($this->status == TransactionStatus::ACTIVE) || ($this->status == TransactionStatus::MARKED_ROLLBACK)) {
            $this->status = TransactionStatus::ROLLING_BACK;

            if (isset($this->resources)) {
                foreach ($this->resources as $resource) {
                    $resource->rollback($this->xid);
                }
            }

            $this->status = TransactionStatus::ROLLEDBACK;
        }
        else {
            throw new IllegalStateException(t(
                "Transaction is in '@status' status. It cannot be rolled back",
                array('@status' => $this->status)));
        }
    }
}
