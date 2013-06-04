<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




class EnvironmentMetaModelFactory extends AbstractMetaModelFactory {

    private static $factory = NULL;

    /**
     * @static
     * @return EnvironmentMetaModelFactory
     */
    public static function getInstance() {
        if (!isset(self::$factory)) {
            self::$factory = new EnvironmentMetaModelFactory();
        }

        return self::$factory;
    }

    protected function getMetaModelPublicNamePrefix() {
        return 'Environment';
    }

    protected function getMetaModelHookName() {
        return 'dc_metamodel_environment_loader';
    }

    protected function initiateMetaModel() {
        return new EnvironmentMetaModel();
    }

    protected function getMetaModelCacheHandler() {
        // we cannot use shared cache to store environment meta model for two reasons:
        //   - it could contain sensitive information such as password
        //   - Catch 22: we need environment meta model already in memory to access configuration of external cache
        return CacheFactory::getInstance()->getLocalCacheHandler($this->getCachePrefix());
    }

    /**
     * @return EnvironmentMetaModel
     */
    public function getMetaModel() {
        return parent::getMetaModel();
    }
}
