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
