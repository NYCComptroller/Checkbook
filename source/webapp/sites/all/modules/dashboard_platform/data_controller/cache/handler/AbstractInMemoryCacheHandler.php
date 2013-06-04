<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
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
        $errorEntryNames = array();

        foreach ($values as $entryName => $value) {
            $result = $this->setInternalValue($entryName, $value, $expiration);
            if ($result === FALSE) {
                $errorEntryNames[] = $entryName;
            }
        }

        return $errorEntryNames;
    }
}
