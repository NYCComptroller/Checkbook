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

namespace Drupal\data_controller\Cache\Factory;

use Drupal\data_controller\Cache\CacheFactory;
use Drupal\data_controller\Cache\Handler\AbstractCacheHandler;
use Drupal\data_controller\Cache\Handler\InMemoryCacheHandler;
use Drupal\data_controller\Cache\Handler\ProxyCacheHandler;
use Drupal\data_controller\Common\NamespaceHelper\NameSpaceHelper;
use Drupal\data_controller\Common\Object\Exception\IllegalArgumentException;
use Drupal\data_controller\Environment\Environment;
use Drupal\data_controller\MetaModel\MetaData\DataSourceMetaData;

class DefaultCacheFactory extends CacheFactory {

    public static $DATASOURCE_NAME__DEFAULT = '__cache';

    private $handlerConfigurations = NULL;
    private $handlerInstances = NULL;
    public $handlers = [];

    public function __construct() {
        parent::__construct();
        $this->handlerConfigurations = \Drupal::moduleHandler()->invokeAll('dc_cache');

        // preparing default value for cache entry expiration time
        $cacheConfigurationSection = Environment::getInstance()->getConfigurationSection('Cache');
        if (isset($cacheConfigurationSection['Entry Expiration']['Default'])) {
            AbstractCacheHandler::$DEFAULT__ENTRY_EXPIRATION = $cacheConfigurationSection['Entry Expiration']['Default'];
        }
    }

    protected function findHandlerConfiguration($type) {
        return isset($this->handlerConfigurations[$type]) ? $this->handlerConfigurations[$type] : NULL;
    }

    protected function prepareHandlerClass($type, $required) {
        $handlerConfiguration = $this->findHandlerConfiguration($type);

        $classname = isset($handlerConfiguration['classname']) ? $handlerConfiguration['classname'] : NULL;
        if (!isset($classname) && $required) {
            throw new IllegalArgumentException(t('Unsupported cache handler: @type', array('@type' => $type)));
        }

        return $classname;
    }

    protected function initializeSharedCacheHandler(DataSourceMetaData $cacheDataSource) {
        $handler = NULL;

        if (isset($cacheDataSource)) {
            $sharedCacheHandlerKey = get_class($this) . '(' . $cacheDataSource->type . ')';
            if (isset($this->handlerInstances[$sharedCacheHandlerKey])) {
                $handler = $this->handlerInstances[$sharedCacheHandlerKey];
            }

            if (!isset($handler)) {
                $classname = $this->prepareHandlerClass($cacheDataSource->type, FALSE);

                if (isset($classname)) {
                    $handler = new $classname(NULL /* we use prefix on ProxyCacheHandler level */, $cacheDataSource);
                    if ($handler->isAccessible()) {
                        $this->handlerInstances[$sharedCacheHandlerKey] = $handler;
                    }
                    else {
                        $handler = NULL;
                    }
                }
            }
        }

        return $handler;
    }

    protected function prepareSharedCacheHandler($cacheDataSourceName) {
        $environment_metamodel = data_controller_get_environment_metamodel();

        $handler = NULL;
        if (isset($cacheDataSourceName)) {
            $cacheDataSource = $environment_metamodel->findDataSource($cacheDataSourceName);
            $handler = $this->initializeSharedCacheHandler($cacheDataSource);
        }
        else {
            $cacheDataSources = $environment_metamodel->findDataSourceByNamespacelessName(self::$DATASOURCE_NAME__DEFAULT);
            if (isset($cacheDataSources)) {
                foreach ($cacheDataSources as $cacheDataSource) {
                    $handler = $this->initializeSharedCacheHandler($cacheDataSource);

                    // selecting first accessible cache
                    if (isset($handler)) {
                        break;
                    }
                }
            }
        }

        return $handler;
    }

    public function getSharedCacheHandler($prefix, $allowCopyInLocalCache = FALSE, $cacheDataSourceName = NULL) {
        $handlerKey = isset($cacheDataSourceName)
            ? NameSpaceHelper::addNameSpace($prefix, NameSpaceHelper::addNameSpace('datasource', $cacheDataSourceName))
            : $prefix;

        if (isset($this->handlerInstances[$handlerKey])) {
            $handler = $this->handlerInstances[$handlerKey];
        }
     	  else {
            $handler = $this->prepareSharedCacheHandler($cacheDataSourceName);
            if (isset($handler)) {
                $serverNamePrefix = '[' . strtoupper(\Drupal::request()->server->get('SERVER_NAME')) . ']';
                $handler = new ProxyCacheHandler(
                    $prefix,
                    new ProxyCacheHandler($serverNamePrefix, $handler, FALSE),
                    $allowCopyInLocalCache);
            }
            else {
                $handler = $this->getLocalCacheHandler($prefix);
            }

            $this->handlers[$handlerKey] = $handler;
        }

        return $handler;
    }

    protected function initializeLocalCache($prefix) {
        $classname = $this->prepareHandlerClass(InMemoryCacheHandler::$CACHE__TYPE, TRUE);

        return new $classname($prefix);
    }

    public function getLocalCacheHandler($prefix) {
        $handlerKey = NameSpaceHelper::addNameSpace($prefix, InMemoryCacheHandler::$CACHE__TYPE);

        if (isset($this->handlerInstances[$handlerKey])) {
            $handler = $this->handlerInstances[$handlerKey];
        }
        else {
            $handler = $this->initializeLocalCache($prefix);
            $this->handlerInstances[$handlerKey] = $handler;
        }

        return $handler;
    }

}
