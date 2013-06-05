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


abstract class TransactionManager extends AbstractObject {

    private static $manager = NULL;

    protected function __construct() {
        parent::__construct();
    }

    /**
     * @static
     * @return TransactionManager
     */
    public static function getInstance() {
        if (!isset(self::$manager)) {
            self::$manager = new DefaultTransactionManager();
        }

        return self::$manager;
    }

    /**
     * Returns a reference to distributed transaction
     *
     * @return Transaction
     */
    abstract public function startTransaction();

    /**
     * Returns a reference to distributed transaction
     * If global transaction has not been started returns a reference to local transaction
     * The local transaction lives until it looses scope. If it is not explicitly committed it will be automatically rolled back
     * Uncommitted distributed transaction will be rolled back automatically when PHP script execution is done
     *
     * @return Transaction
     */
    abstract public function getTransaction();
}
