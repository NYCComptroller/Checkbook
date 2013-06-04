<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




abstract class CacheFactory extends AbstractFactory {

    /**
     * @var CacheFactory
     */
    private static $factory = NULL;

    /**
     * @static
     * @return CacheFactory
     */
    public static function getInstance() {
        if (!isset(self::$factory)) {
            self::$factory = new DefaultCacheFactory();
        }

        return self::$factory;
    }

    /**
     * @param string $prefix
     * @param boolean $allowCopyInLocalCache
     * @param string|null $cacheDataSourceName
     * @return CacheHandler
     */
    abstract public function getSharedCacheHandler($prefix, $allowCopyInLocalCache = FALSE, $cacheDataSourceName = NULL);

    /**
     * @param string $prefix
     * @return CacheHandler
     */
    abstract public function getLocalCacheHandler($prefix);
}
