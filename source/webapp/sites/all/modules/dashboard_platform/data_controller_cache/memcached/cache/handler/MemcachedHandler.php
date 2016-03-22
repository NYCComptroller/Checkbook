<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class MemcachedHandler extends AbstractSharedCacheHandler {

    public static $CACHE__TYPE = 'Memcached';

    protected $accessible = FALSE;

    protected function initialize($prefix, DataSourceMetaData $datasource) {
        $this->accessible = function_exists('dmemcache_get') && function_exists('dmemcache_get');

        return TRUE;
    }

    public function isAccessible() {
        return parent::isAccessible() && $this->accessible;
    }

    protected function getInternalValue($entryName) {
        return dmemcache_get($entryName);
    }

    protected function getInternalValues($entryNames) {
        return dmemcache_get_multi($entryNames);
    }

    protected function setInternalValue($entryName, $value, $expiration) {
        return dmemcache_set($entryName, $value, $expiration);
    }

    protected function setInternalValues($values, $expiration) {
        $unused = FALSE;
        return dmemcache_set($values, $unused, $expiration);
    }
}
