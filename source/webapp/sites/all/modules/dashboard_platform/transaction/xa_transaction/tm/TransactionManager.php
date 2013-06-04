<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
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
