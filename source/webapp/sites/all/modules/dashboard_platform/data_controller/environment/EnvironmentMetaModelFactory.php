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
