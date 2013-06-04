<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


abstract class AbstractSingleton {

    protected function __construct() {}

    public function __destruct() {}

    final public function __clone() {
        throw new UnsupportedOperationException();
    }
}
