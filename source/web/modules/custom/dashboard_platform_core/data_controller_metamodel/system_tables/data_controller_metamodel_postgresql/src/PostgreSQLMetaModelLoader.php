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

namespace Drupal\data_controller_metamodel_postgresql;

use Drupal\data_controller\Controller\CallContext\DataControllerCallContext;
use Drupal\data_controller\Datasource\DataSourceQueryFactory;
use Drupal\data_controller\Datasource\Formatter\Handler\PassthroughResultFormatter;
use Drupal\data_controller\MetaModel\MetaData\DataSourceMetaData;
use Drupal\data_controller_metamodel_system_tables\AbstractSystemTableMetaModelLoader;
use Drupal\data_controller_postgresql\PostgreSQLDataSource;

class PostgreSQLMetaModelLoader extends AbstractSystemTableMetaModelLoader {


  protected function selectedDataSourceType() {
        return PostgreSQLDataSource::TYPE;
    }

    protected function prepareColumnsMetaDataProperties(DataSourceMetaData $datasource, array $tableNames) {
        $datasourceHandler = DataSourceQueryFactory::getInstance()->getHandler($datasource->type);

        $sql = 'SELECT c.relname AS ' . self::PROPERTY__TABLE_NAME . ', '
             . '       a.attname AS ' . self::PROPERTY__COLUMN_NAME . ', '
             . '       a.attnum AS ' . self::PROPERTY__COLUMN_INDEX . ', '
             . '       t.typname AS ' . self::PROPERTY__COLUMN_TYPE
             . '  FROM pg_class c INNER JOIN pg_namespace ns ON ns.oid = c.relnamespace'
             . '       INNER JOIN pg_attribute a ON a.attrelid = c.oid'
             . '       INNER JOIN pg_type t ON t.oid = a.atttypid'
             . " WHERE c.relname IN ('" .  implode("', '", $tableNames) . "')"
             . "   AND c.relkind IN ('r','v')"
             . "   AND ns.nspname = '$datasource->schema'"
             . '   AND a.attnum > 0';

        //LogHelper::log_notice(new StatementLogMessage('metadata.dataset.systemTable'.PHP_EOL, $sql));

        return $datasourceHandler->executeQuery(new DataControllerCallContext(), $datasource, $sql, new PassthroughResultFormatter());
    }
}
