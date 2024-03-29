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

namespace Drupal\join_controller;

use Drupal\data_controller\Common\Pattern\Factory\AbstractFactory;
use Drupal\join_controller\Controller\Factory\DefaultJoinControllerFactory;
use Drupal\join_controller\Controller\JoinController;

abstract class JoinControllerFactory extends AbstractFactory {

    private static $factory = NULL;

    /**
     * @static
     * @return JoinControllerFactory
     */
    public static function getInstance() {
        if (!isset(self::$factory)) {
            self::$factory = new DefaultJoinControllerFactory();
        }

        return self::$factory;
    }

    /**
     * @param $method
     * @return JoinController
     */
    abstract public function getHandler($method);

    abstract public function getSupportedMethods();
}
