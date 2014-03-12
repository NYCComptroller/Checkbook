<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class MemcachedHandler extends AbstractSharedCacheHandler {

    public static $CACHE__TYPE = 'Memcached';

    private $memcached = NULL;

    protected function initialize($prefix, DataSourceMetaData $datasource) {
        $result = TRUE;

        if (class_exists('Memcached')) {
            $this->memcached = new Memcached();

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
                $this->memcached = NULL;
            }

            if ($unsuccessfulRegistrationCount > 0) {
                $result = FALSE;
            }
        }

        return $result;
    }

    protected function registerServer($host, $port) {
        $result = $this->memcached->addServer($host, $port);
        if (!$result) {
            LogHelper::log_error(t(
                '[@cacheType] Could not add server (@host:@port): @message',
                array(
                    '@cacheType' => self::$CACHE__TYPE,
                    '@host' => $host,
                    '@port' => $port,
                    '@message' => $this->memcached->getResultMessage())));
        }

        return $result;
    }

    public function isAccessible() {
        return parent::isAccessible() && isset($this->memcached);
    }

    protected function getInternalValue($entryName) {
        $value = $this->memcached->get($entryName);

        if ($value === FALSE) {
            if ($this->memcached->getResultCode() != Memcached::RES_NOTFOUND) {
                LogHelper::log_error(t(
                    '[@cacheType] Could not get value (@entryName): @message',
                    array(
                        '@cacheType' => self::$CACHE__TYPE,
                        '@entryName' => $entryName,
                        '@message' => $this->memcached->getResultMessage())));
            }
        }

        return $value;
    }

    protected function getInternalValues($entryNames) {
        $values = $this->memcached->getMulti($entryNames);

        if ($values === FALSE) {
            if ($this->memcached->getResultCode() != Memcached::RES_NOTFOUND) {
                LogHelper::log_error(t(
                    '[@cacheType] Could not get values (@entryNames): @message',
                    array(
                        '@cacheType' => self::$CACHE__TYPE,
                        '@entryNames' => implode(', ', $entryNames),
                        '@message' => $this->memcached->getResultMessage())));
            }
        }

        return $values;
    }

    protected function setInternalValue($entryName, $value, $expiration) {
        $result = $this->memcached->set($entryName, $value, $expiration);

        if ($result === FALSE) {
            LogHelper::log_error(t(
                '[@cacheType] Internal error during entry value storing: @message',
                array('@cacheType' => self::$CACHE__TYPE, '@message' => $this->memcached->getResultMessage())));
        }

        return $result;
    }

    protected function setInternalValues($values, $expiration) {
        $result = $this->memcached->setMulti($values, $expiration);

        if ($result === FALSE) {
            LogHelper::log_error(t(
                '[@cacheType] Internal error during entry value storing: @message',
                array('@cacheType' => self::$CACHE__TYPE, '@message' => $this->memcached->getResultMessage())));
        }

        return $result === TRUE ? array() : array_keys($values);
    }
}
