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

namespace Drupal\data_controller\Datasource\Dataset\Assembler\Factory;

use Drupal\data_controller\Common\Object\Exception\IllegalArgumentException;
use Drupal\data_controller\Datasource\Dataset\Assembler\DatasetSourceAssemblerFactory;

class DefaultDatasetSourceAssemblerFactory extends DatasetSourceAssemblerFactory {

    private $handlerConfigurations = NULL;

    public function __construct() {
        parent::__construct();
        $this->handlerConfigurations = \Drupal::moduleHandler()->invokeAll('dc_dataset_assembler');
    }

    protected function getHandlerConfiguration($type) {
        if (!isset($this->handlerConfigurations[$type])) {
            throw new IllegalArgumentException(t('Unsupported dataset assembler: @type', array('@type' => $type)));
        }

        return $this->handlerConfigurations[$type];
    }

    public function getHandler($assemblerType, $assemblerConfiguration) {
        $handlerConfiguration = $this->getHandlerConfiguration($assemblerType);
        $classname = $handlerConfiguration['classname'];

        return new $classname($assemblerConfiguration);
    }
}
