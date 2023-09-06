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

namespace Drupal\data_controller_memcached\Memcached\Environment\Loader\Handler;

use Drupal\checkbook_log\LogHelper;
use Drupal\data_controller\Cache\Factory\DefaultCacheFactory;
use Drupal\data_controller\Common\NamespaceHelper\NameSpaceHelper;
use Drupal\data_controller\MetaModel\Factory\AbstractMetaModelFactory;
use Drupal\data_controller\MetaModel\Handler\AbstractMetaModel;
use Drupal\data_controller\MetaModel\Loader\Handler\AbstractMetaModelLoader;
use Drupal\data_controller\MetaModel\MetaData\DataSourceMetaData;
use Drupal\data_controller_memcached\Memcached\Cache\Handler\MemcachedHandler;

class MemcachedEnvironmentMetaModelGenerator extends AbstractMetaModelLoader {

    public function load(AbstractMetaModelFactory $factory, AbstractMetaModel $environment_metamodel, array $filters = NULL, $finalAttempt) {
        LogHelper::log_notice(t('Generating Environment Meta Model for Memcached cache ...'));

        $datasourceCount = 0;

        $datasource = new DataSourceMetaData();
        $datasource->name = NameSpaceHelper::addNameSpace(MemcachedHandler::$CACHE__TYPE, DefaultCacheFactory::$DATASOURCE_NAME__DEFAULT);
        $datasource->type = MemcachedHandler::$CACHE__TYPE;
        $environment_metamodel->registerDataSource($datasource);
        $datasourceCount++;

        LogHelper::log_info(t('Generated @datasourceCount data sources', array('@datasourceCount' => $datasourceCount)));

        return self::LOAD_STATE__SUCCESSFUL;
    }
}
