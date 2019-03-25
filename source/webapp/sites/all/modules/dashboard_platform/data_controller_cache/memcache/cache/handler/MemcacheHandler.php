<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class MemcacheHandler extends AbstractSharedCacheHandler {

    public static $CACHE__TYPE = 'Memcache';

    public static $DEFAULT__COMPRESSION_THRESHOLD = 10240; // bytes
    public static $DEFAULT__COMPRESSION_SAVINGS_MIN = 0.3; // 30% savings

    /**
     * @var Memcache | null
     */
    private $memcache = NULL;

    protected function initialize($prefix, DataSourceMetaData $datasource) {
        $result = TRUE;

        if (class_exists('Memcache')) {
            $this->memcache = new Memcache();

            $successfulRegistrationCount = $unsuccessfulRegistrationCount = 0;

            // adding servers
            if (isset($datasource->host)) {
                $serverResult = $this->registerServer($datasource->host, $datasource->port);
                if ($serverResult) {
                    $successfulRegistrationCount++;
                }
                else {
                    $unsuccessfulRegistrationCount++;
                }
            }
            if (isset($datasource->servers)) {
                foreach ($datasource->servers as $server) {
                    $serverResult = $this->registerServer($server->host, $server->port);
                    if ($serverResult) {
                        $successfulRegistrationCount++;
                    }
                    else {
                        $unsuccessfulRegistrationCount++;
                    }
                }
            }

            if ($successfulRegistrationCount == 0) {
                $this->memcache = NULL;
            }
            else {
                $this->memcache->setCompressThreshold(self::$DEFAULT__COMPRESSION_THRESHOLD, self::$DEFAULT__COMPRESSION_SAVINGS_MIN);
            }

            if ($unsuccessfulRegistrationCount > 0) {
                $result = FALSE;
            }
        }

        return $result;
    }

    public function __destruct() {
        if (isset($this->memcache)) {
            $this->memcache->close();
            $this->memcache = NULL;
        }
        parent::__destruct();
    }

    protected function registerServer($host, $port) {
        $result = $this->memcache->addServer($host, $port);
        if (!$result) {
            LogHelper::log_error(t(
                '[@cacheType] Could not add server (@host:@port)',
                array(
                    '@cacheType' => self::$CACHE__TYPE,
                    '@host' => $host,
                    '@port' => $port)));
        }

        return $result;
    }

    public function isAccessible() {
        return parent::isAccessible() && isset($this->memcache);
    }

    protected function getInternalValue($entryName) {
        return $this->memcache->get($entryName);
    }

    protected function getInternalValues($entryNames) {
        return $this->memcache->get($entryNames);
    }

    protected function setInternalValue($entryName, $value, $expiration) {
        return $this->memcache->set($entryName, $value, 0, $expiration);
    }

    protected function setInternalValues($values, $expiration) {
        $errorEntryNames = [];

        foreach ($values as $entryName => $value) {
            if ($this->memcache->set($entryName, $value, 0, $expiration) === FALSE) {
                $errorEntryNames[] = $entryName;
            }
        }

        return $errorEntryNames;
    }
}
