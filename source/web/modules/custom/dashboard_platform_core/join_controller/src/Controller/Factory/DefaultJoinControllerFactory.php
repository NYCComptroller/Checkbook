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

namespace Drupal\join_controller\Controller\Factory;

use Drupal\data_controller\Common\Object\Exception\IllegalArgumentException;
use Drupal\join_controller\JoinControllerFactory;

class DefaultJoinControllerFactory extends JoinControllerFactory {

    private $handlerConfigurations = NULL;
    private $handlerInstances = NULL;

    protected function __construct() {
        parent::__construct();
        $this->handlerConfigurations = \Drupal::moduleHandler()->invokeAll('jc_method');
    }

    protected function getHandlerConfiguration($method) {
        if (isset($this->handlerConfigurations[$method])) {
            return $this->handlerConfigurations[$method];
        }

        throw new IllegalArgumentException(t('Unsupported join method: @method', array('@method' => $method)));
    }

    public function getHandler($method) {
        if (isset($this->handlerInstances[$method])) {
            return $this->handlerInstances[$method];
        }

        $handlerConfiguration = $this->getHandlerConfiguration($method);
        $classname = $handlerConfiguration['classname'];

        $handler = new $classname();

        $this->handlerInstances[$method] = $handler;

        return $handler;
    }

    public function getSupportedMethods() {
        return array_keys($this->handlerConfigurations);
    }
}
