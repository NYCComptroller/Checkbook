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
        $errorEntryNames = array();

        foreach ($values as $entryName => $value) {
            if ($this->memcache->set($entryName, $value, 0, $expiration) === FALSE) {
                $errorEntryNames[] = $entryName;
            }
        }

        return $errorEntryNames;
    }
}
