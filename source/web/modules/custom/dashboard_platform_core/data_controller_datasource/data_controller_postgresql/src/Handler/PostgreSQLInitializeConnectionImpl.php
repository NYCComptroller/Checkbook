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

namespace Drupal\data_controller_postgresql\Handler;

use Drupal\data_controller\Common\Object\Exception\IllegalStateException;
use Drupal\data_controller\Datasource\DataSourceHandler;
use Drupal\data_controller\MetaModel\MetaData\DataSourceMetaData;
use Drupal\data_controller_sql\Datasource\Handler\Impl\AbstractInitializePDOConnectionImpl;
use PDO;

class PostgreSQLInitializeConnectionImpl extends AbstractInitializePDOConnectionImpl {

    public function initializePDOConnection(DataSourceHandler $handler, DataSourceMetaData $datasource) {
        $dsn = "pgsql:host=$datasource->host";
        if (isset($datasource->port)) {
            $dsn .= ";port=$datasource->port";
        }
        if (isset($datasource->database)) {
            $dsn .= ";dbname=$datasource->database";
        }

        if (!extension_loaded('pdo_pgsql')) {
            throw new IllegalStateException(t("'PostgreSQL PDO' PHP extension is not loaded"));
        }

        return new PDO($dsn, $datasource->username, $datasource->password);
    }
}
