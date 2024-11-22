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

namespace Drupal\data_controller\MetaModel\MetaData;

use Drupal\data_controller\Common\Object\Manipulation\ObjectHelper;

class DataSourceMetaData extends AbstractMetaData {

    public $database;
    public $username;
    public $password;
    public $host;
    public $port;
    public $prefix;
    public $schema;
    public $namespace;
    public $autoload;
    public $pdo;
    public $init_commands = [];
    public $charset;
    public $collation;

    public $parentName = NULL;
    public $type = NULL;

    public $readonly = NULL;
    public $system = NULL;

    public $shared = NULL;

    public function initializeInstanceFrom($sourceDataSource) {
        // we need to support some unknown composite properties
        ObjectHelper::mergeWith($this, $sourceDataSource, TRUE);
    }

    public function isReadOnly() {
        return isset($this->readonly) ? $this->readonly : FALSE;
    }

    public function isSystem() {
        return isset($this->system) ? $this->system : FALSE;
    }

    public function isShared() {
        return isset($this->shared) ? $this->shared : FALSE;
    }
}
