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


class ProxyCacheHandler extends AbstractObject implements CacheHandler {

    protected $prefix = NULL;

    private $handler = NULL;
    private $localCacheHandler = NULL;

    public function __construct($prefix, CacheHandler $handler, $allowCopyInLocalCache) {
        parent::__construct();
        $this->prefix = $prefix;

        $this->handler = $handler;

        if ($allowCopyInLocalCache) {
            $this->localCacheHandler = new InMemoryCacheHandler($prefix);
        }
    }

    public function getCacheType() {
        return $this->handler->getCacheType() . '[Proxy]';
    }

    public function isAccessible() {
        return TRUE;
    }

    protected function assembleCacheEntryName($name) {
        return isset($this->prefix) ? NameSpaceHelper::addNameSpace($this->prefix, $name) : $name;
    }

    public function getValue($name) {
        $value = isset($this->localCacheHandler) ? $this->localCacheHandler->getValue($name) : NULL;

        // could not find in local cache
        if (!isset($value)) {
            $externalCacheEntryName = $this->assembleCacheEntryName($name);

            // reading from external cache
            $value = $this->handler->getValue($externalCacheEntryName);

            // storing loaded value in internal cache for the future use
            if (isset($this->localCacheHandler)) {
                $this->localCacheHandler->setValue($name, $value);
            }
        }

        return $value;
    }

    public function getValues(array $names) {
        $values = isset($this->localCacheHandler) ? $this->localCacheHandler->getValues($names) : NULL;

        $missingExternalCacheEntryNames = NULL;
        foreach ($names as $name) {
            if (!isset($values[$name])) {
                $missingExternalCacheEntryNames[$name] = $this->assembleCacheEntryName($name);
            }
        }

        if (isset($missingExternalCacheEntryNames)) {
            // loading all missing values from external cache
            $missingValues = $this->handler->getValues($missingExternalCacheEntryNames);
            // processing loaded values
            if (isset($missingValues)) {
                foreach ($missingValues as $externalCacheEntryName => $value) {
                    $name = array_search($externalCacheEntryName, $missingExternalCacheEntryNames);
                    if ($name === FALSE) {
                        throw new IllegalStateException(t(
                            'Could not find entry name by the external cache entry name: @externalCacheEntryName',
                            array('@externalCacheEntryName' => $externalCacheEntryName)));
                    }

                    $values[$name] = $value;

                    // storing loaded value in internal cache for the future use
                    if (isset($this->localCacheHandler)) {
                        $this->localCacheHandler->setValue($name, $value);
                    }
                }
            }
        }

        return $values;
    }

    public function setValue($name, $value, $expiration = NULL) {
        if (isset($this->localCacheHandler)) {
            $this->localCacheHandler->setValue($name, $value, $expiration);
        }

        $externalCacheEntryName = $this->assembleCacheEntryName($name);
        $this->handler->setValue($externalCacheEntryName, $value, $expiration);
    }

    public function setValues($values, $expiration = NULL) {
        if (isset($this->localCacheHandler)) {
            $this->localCacheHandler->setValues($values, $expiration);
        }

        $externalCacheValues = NULL;
        if (isset($values)) {
            foreach ($values as $name => $value) {
                $externalCacheEntryName = $this->assembleCacheEntryName($name);

                $externalCacheValues[$externalCacheEntryName] = $value;
            }
        }

        $this->handler->setValues($externalCacheValues, $expiration);
    }
}
