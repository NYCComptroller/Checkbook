<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class APCHandler extends AbstractSharedCacheHandler {

    public static $CACHE__TYPE = 'APC';

    protected $accessible = FALSE;

    protected function initialize($prefix, DataSourceMetaData $datasource) {
        $this->accessible = function_exists('apc_fetch') && function_exists('apc_store');

        return TRUE;
    }

    public function isAccessible() {
        return parent::isAccessible() && $this->accessible;
    }

    protected function getInternalValue($entryName) {
        return apc_fetch($entryName);
    }

    protected function getInternalValues($entryNames) {
        return apc_fetch($entryNames);
    }

    protected function setInternalValue($entryName, $value, $expiration) {
        return apc_store($entryName, $value, $expiration);
    }

    protected function setInternalValues($values, $expiration) {
        $unused = FALSE;

        return apc_store($values, $unused, $expiration);
    }
}
