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
