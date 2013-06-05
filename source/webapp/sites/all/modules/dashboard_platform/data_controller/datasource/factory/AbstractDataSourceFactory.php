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


abstract class AbstractDataSourceFactory extends AbstractFactory {

    private $handlerConfigurations = NULL;
    private $extensionConfigurations = NULL;
    private $handlerInstances = NULL;

    protected function __construct() {
        parent::__construct();

        $this->extensionConfigurations = module_invoke_all('dc_datasource');
        $this->handlerConfigurations = module_invoke_all($this->getHookName());
    }

    abstract protected function getFactoryPublicNamePrefix();

    abstract protected function getHookName();

    protected function getExtensionConfiguration($type) {
        return isset($this->extensionConfigurations[$type]) ? $this->extensionConfigurations[$type] : NULL;
    }

    protected function getHandlerConfiguration($type) {
        if (!isset($this->handlerConfigurations[$type])) {
            $prefix = $this->getFactoryPublicNamePrefix();
            throw new IllegalArgumentException(t("Unsupported $prefix Data Source handler: @type", array('@type' => $type)));
        }

        return $this->handlerConfigurations[$type];
    }

    public function getHandler($type) {
        if (isset($this->handlerInstances[$type])) {
            return $this->handlerInstances[$type];
        }

        $handlerConfiguration = $this->getHandlerConfiguration($type);
        $extensionConfiguration = $this->getExtensionConfiguration($type);

        $combinedExtensionConfigurations = NULL;
        // adding generic configuration
        ArrayHelper::mergeArrays($combinedExtensionConfigurations, $extensionConfiguration['extensions']);
        // adding handler specific configurations
        if (isset($handlerConfiguration['extensions'])) {
            ArrayHelper::mergeArrays($combinedExtensionConfigurations, $handlerConfiguration['extensions']);
            unset($handlerConfiguration['extensions']);
        }

        $classname = $handlerConfiguration['handler'];

        $handler = new $classname($type, $combinedExtensionConfigurations);

        $this->handlerInstances[$type] = $handler;

        return $handler;
    }
}
