<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




class DefaultCacheFactory extends CacheFactory {

    public static $DATASOURCE_NAME__DEFAULT = '__cache';

    private $handlerConfigurations = NULL;
    private $handlerInstances = NULL;

    public function __construct() {
        parent::__construct();
        $this->handlerConfigurations = module_invoke_all('dc_cache');

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
                $handler = new ProxyCacheHandler($prefix, $handler, $allowCopyInLocalCache);
            }
            else {
                $handler = $this->getLocalCacheHandler($prefix);
            }

            $this->handlerInstances[$handlerKey] = $handler;
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
