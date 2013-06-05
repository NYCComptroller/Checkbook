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


abstract class AbstractSharedCacheHandler extends AbstractCacheHandler {

    public function __construct($prefix, DataSourceMetaData $datasource) {
        LogHelper::log_notice(t('[@cacheType] Initializing PHP extension ...', array('@cacheType' => $this->getCacheType())));

        // taking into account possible datasource's nested name space
        $adjustedPrefix = isset($datasource->nestedNameSpace) ? NameSpaceHelper::addNameSpace($prefix, $datasource->nestedNameSpace) : $prefix;
        parent::__construct($adjustedPrefix);

        // the optional datasource can have its own expiration schedule
        if (isset($datasource->entryExpiration)) {
            $this->entryExpiration = $datasource->entryExpiration;
        }

        if ($this->initialize($prefix, $datasource) !== FALSE) {
            $this->checkAccessibility(FALSE);
        }
    }

    // returns FALSE if initialization failed
    abstract protected function initialize($prefix, DataSourceMetaData $datasource);

    protected function assembleCacheEntryName($name) {
        $cacheEntryName = parent::assembleCacheEntryName($name);

        // some cache storages do not support space in key name
        $adjustedCacheEntryName = str_replace(' ', '_', $cacheEntryName);

        return $adjustedCacheEntryName;
    }

    public function getValue($name) {
        $timeStart = microtime(TRUE);

        $value = parent::getValue($name);

        LogHelper::log_info(t(
            "[@cacheType] Execution time for retrieving '@entryName' entry is !executionTime. @successFlag",
            array(
                '@cacheType' => $this->getCacheType(),
                '@entryName' => $this->assembleCacheEntryName($name),
                '!executionTime' => ExecutionPerformanceHelper::formatExecutionTime($timeStart),
                '@successFlag' => (isset($value) ? 'CACHE HIT' : 'Cache NOT hit'))));

        return $value;
    }

    public function getValues(array $names) {
        $timeStart = microtime(TRUE);

        $values = parent::getValues($names);

        $nameCount = count($names);
        $loadedValueCount = count($values);

        LogHelper::log_debug(t(
            '[@cacheType] Requested entries: @entryNames',
            array(
                '@cacheType' => $this->getCacheType(),
                '@entryNames' => ArrayHelper::printArray(array_values($names), ', ', TRUE, FALSE))));
        LogHelper::log_debug(
            t('[@cacheType] Retrieved entries: @entryNames',
                array(
                    '@cacheType' => $this->getCacheType(),
                    '@entryNames' => (($nameCount == $loadedValueCount)
                        ? 'ALL'
                        : (isset($values) ? ArrayHelper::printArray(array_keys($values), ', ', TRUE, FALSE) : 'NONE')))));
        LogHelper::log_info(t(
            '[@cacheType] Execution time for retrieving @entryCount entries is !executionTime. @successFlag',
            array(
                '@cacheType' => $this->getCacheType(),
                '@entryCount' => $nameCount,
                '!executionTime' => ExecutionPerformanceHelper::formatExecutionTime($timeStart),
                '@successFlag' => (
                    isset($values)
                        ? (($nameCount == $loadedValueCount) ? 'Cache HIT' : "Cache hit for ONLY $loadedValueCount entries out of $nameCount")
                        : 'Cache NOT hit'))));

        return $values;
    }

    public function setValue($name, $value, $expiration = NULL) {
        $timeStart = microtime(TRUE);

        $result = parent::setValue($name, $value, $expiration);

        LogHelper::log_info(t(
            "[@cacheType] Execution time for @successFlag storing '@entryName' entry is !executionTime",
            array(
                '@cacheType' => $this->getCacheType(),
                '@entryName' => $this->assembleCacheEntryName($name),
                '!executionTime' => ExecutionPerformanceHelper::formatExecutionTime($timeStart),
                '@successFlag' => (($result === FALSE) ? 'UNSUCCESSFUL' : 'SUCCESSFUL'))));

        return $result;
    }

    public function setValues($values, $expiration = NULL) {
        $timeStart = microtime(TRUE);

        $errorEntryNames = parent::setValues($values, $expiration);

        $entryCount = count($values);
        $errorEntryCount = count($errorEntryNames);
        $successfulEntryCount = $entryCount - $errorEntryCount;

        LogHelper::log_info(t(
            "[@cacheType] Execution time for @successFlag storing of @entryCount entries is !executionTime",
            array(
                '@cacheType' => $this->getCacheType(),
                '@entryCount' => (
                    ($errorEntryCount == 0)
                        ? $entryCount // no error at all
                        : (($successfulEntryCount == 0)
                            ? $entryCount // all errors
                            : "$successfulEntryCount out of $entryCount")), // some errors but also some success
                '!executionTime' => ExecutionPerformanceHelper::formatExecutionTime($timeStart),
                '@successFlag' => (
                    ($errorEntryCount == 0)
                        ? 'SUCCESSFUL' // no error at all
                        : (($successfulEntryCount == 0)
                            ? 'UNSUCCESSFUL' // all errors
                            : 'successful'))))); // some errors but also some success

        return $errorEntryNames;
    }
}
