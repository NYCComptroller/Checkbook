<?php
/**
 * This file is part of the Checkbook NYC financial transparency software.
 *
 * Copyright (c) 2012 – 2023 New York City
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Drupal\data_controller_memcached\Memcached\Cache\Handler;

use Drupal\data_controller\Cache\Handler\AbstractSharedCacheHandler;
use Drupal\data_controller\MetaModel\MetaData\DataSourceMetaData;

class MemcachedHandler extends AbstractSharedCacheHandler {

    public static $CACHE__TYPE = 'Memcached';

    protected $accessible = FALSE;

    protected function initialize($prefix, DataSourceMetaData $datasource) {
      $drupal_state = \Drupal::state();
      $this->accessible = method_exists($drupal_state, 'get') && method_exists($drupal_state, 'set');

      return TRUE;
    }

    public function isAccessible() {
        return parent::isAccessible() && $this->accessible;
    }

    protected function getInternalValue($entryName) {
        return \Drupal::state()->get($entryName);
    }

    protected function getInternalValues($entryNames) {
        return \Drupal::state()->get($entryNames);
    }

    protected function setInternalValue($entryName, $value, $expiration) {
        return \Drupal::state()->set($entryName, $value, $expiration);
    }

    protected function setInternalValues($values, $expiration) {
        $unused = FALSE;
        return \Drupal::state()->set($values, $unused, $expiration);
    }
}
