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

namespace Drupal\data_controller\Datasource\Operator\Handler;

use Drupal\data_controller\Common\Datatype\DataTypeFactory;
use Drupal\data_controller\Common\Object\Manipulation\StringHelper;
use Drupal\data_controller\Datasource\Operator\ParameterBasedOperatorHandler;

abstract class AbstractRangeBasedOperatorHandler extends AbstractOperatorHandler implements ParameterBasedOperatorHandler {

    public $from = NULL;
    public $to = NULL;

    public function __construct($configuration, $from = NULL, $to = NULL) {
        parent::__construct($configuration);

        $this->from = StringHelper::trim($from);
        $this->to = StringHelper::trim($to);
    }

    public function getParameterDataType() {
        return DataTypeFactory::getInstance()->autoDetectPrimaryDataType(array($this->from, $this->to));
    }
}
