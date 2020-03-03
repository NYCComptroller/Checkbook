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




abstract class AbstractCacheHandler extends AbstractObject implements CacheHandler {

    public static $DEFAULT__ENTRY_EXPIRATION = 3600; // seconds

    protected $prefix = NULL;
    protected $entryExpiration = NULL;

    public function __construct($prefix) {
        parent::__construct();
        $this->prefix = $prefix;
        $this->entryExpiration = self::$DEFAULT__ENTRY_EXPIRATION;
    }

    public function getCacheType() {
        $className = get_class($this);

        return isset($className::$CACHE__TYPE) ? $className::$CACHE__TYPE : 'Unknown Cache';
    }

    public function isAccessible() {
        return TRUE;
    }

    protected function checkAccessibility($raiseError) {
        if (!$this->isAccessible()) {
            $message = t("'@cacheType' cache cannot be used at this time", array('@cacheType' => $this->getCacheType()));
            if ($raiseError) {
                throw new IllegalStateException($message);
            }
            else {
                LogHelper::log_error($message);
            }
        }
    }

    protected function assembleCacheEntryName($name) {
        return isset($this->prefix) ? NameSpaceHelper::addNameSpace($this->prefix, $name) : $name;
    }

    protected function assembleCacheEntryNames($names) {
        $cacheEntryNames = NULL;

        foreach ($names as $name) {
            $cacheEntryNames[$name] = $this->assembleCacheEntryName($name);
        }

        return $cacheEntryNames;
    }

    // should return value or FALSE
    abstract protected function getInternalValue($entryName);

    public function getValue($name) {
        $this->checkAccessibility(TRUE);

        $entryName = $this->assembleCacheEntryName($name);

        try {
            $value = $this->getInternalValue($entryName);
        }
        catch (Exception $e) {
            LogHelper::log_error($e);
            $value = FALSE;
        }

        if ($value === FALSE) {
            $value = NULL;
        }

        return $value;
    }

    // should return list of values or FALSE
    abstract protected function getInternalValues($entryNames);

    public function getValues(array $names) {
        $this->checkAccessibility(TRUE);

        $entryNames = $this->assembleCacheEntryNames($names);

        try {
            $values = $this->getInternalValues($entryNames);
        }
        catch (Exception $e) {
            LogHelper::log_error($e);
            $values = FALSE;
        }

        if ($values === FALSE) {
            return NULL;
        }

        // updating name mappings
        $adjustedValues = NULL;
        foreach ($values as $entryName => $value) {
            $name = array_search($entryName, $entryNames);
            if ($name === FALSE) {
                throw new IllegalStateException(t(
                    'Could not find entry name by the cache entry name: @entryName',
                    array('@entryName' => $entryName)));
            }

            $adjustedValues[$name] = $value;
        }

        return $adjustedValues;
    }

    protected function adjustExpiration($expiration = NULL) {
        return isset($expiration) ? $expiration : $this->entryExpiration;
    }

    // should return TRUE or FALSE
    abstract protected function setInternalValue($entryName, $value, $expiration);

    public function setValue($name, $value, $expiration = NULL) {
        $this->checkAccessibility(TRUE);

        $entryName = $this->assembleCacheEntryName($name);

        $adjustedExpiration = $this->adjustExpiration($expiration);

        $result = $this->setInternalValue($entryName, $value, $adjustedExpiration);

        if ($result === FALSE) {
            LogHelper::log_error(t(
                "[@cacheType] Could not set value for the entry: @entryName",
                array(
                    '@cacheType' => $this->getCacheType(),
                    '@entryName' => $entryName)));
        }

        return $result;
    }

    // should return names of entries which could not be stored
    abstract protected function setInternalValues($values, $expiration);

    public function setValues($values, $expiration = NULL) {
        $this->checkAccessibility(TRUE);

        $adjustedValues = [];
        if (isset($values)) {
            foreach ($values as $name => $value) {
                $entryName = $this->assembleCacheEntryName($name);

                $adjustedValues[$entryName] = $value;
            }
        }
        if (count($adjustedValues) == 0) {
            return $adjustedValues;
        }

        $adjustedExpiration = $this->adjustExpiration($expiration);

        $errorEntryNames = $this->setInternalValues($adjustedValues, $adjustedExpiration);
        if (count($errorEntryNames) > 0) {
            LogHelper::log_error(t(
                "[@cacheType] Could not set values for the entries: @entryNames",
                array(
                    '@cacheType' => $this->getCacheType(),
                    '@entryNames' => implode(', ', $errorEntryNames))));
        }

        return $errorEntryNames;
    }
}
