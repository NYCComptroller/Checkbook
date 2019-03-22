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


abstract class AbstractInMemoryCacheHandler extends AbstractCacheHandler {

    private $cache = NULL;

    protected function getInternalValue($entryName) {
        return isset($this->cache[$entryName]) ? clone $this->cache[$entryName] : FALSE;
    }

    protected function getInternalValues($entryNames) {
        $values = NULL;

        foreach ($entryNames as $entryName) {
            $value = $this->getInternalValue($entryName);
            if ($value !== FALSE) {
                $values[$entryName] = $value;
            }
        }

        return isset($values) ? $values : FALSE;
    }

    protected function setInternalValue($entryName, $value, $expiration) {
        // Note: we do not need to support $expiration parameter because instance of this class lives only for time of the request

        // we need to clone the value to preserve further modification of values in this cache
        if (isset($value)) {
            $this->cache[$entryName] = clone $value;
        }
        else {
            unset($this->cache[$entryName]);
        }

        return TRUE;
    }

    protected function setInternalValues($values, $expiration) {
        $errorEntryNames = [];

        foreach ($values as $entryName => $value) {
            $result = $this->setInternalValue($entryName, $value, $expiration);
            if ($result === FALSE) {
                $errorEntryNames[] = $entryName;
            }
        }

        return $errorEntryNames;
    }
}
