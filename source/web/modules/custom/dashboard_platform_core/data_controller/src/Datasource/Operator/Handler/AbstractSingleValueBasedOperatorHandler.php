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
use Drupal\data_controller\Common\Object\Exception\IllegalArgumentException;
use Drupal\data_controller\Common\Object\Manipulation\StringHelper;

abstract class AbstractSingleValueBasedOperatorHandler extends AbstractSingleParameterBasedOperatorHandler {

    public function __construct($configuration, $value = NULL) {
        parent::__construct($configuration);

        if (is_array($value)) {
            $values = (array) $value;
            throw new IllegalArgumentException(t(
            	'Only single value is supported for the operator: [@value]',
                array('@value' => implode(', ', $values))));
        }

        $parameterName = $this->getParameterName();
        $this->$parameterName = StringHelper::trim($value);
    }

    public function getParameterDataType() {
        $parameterName = $this->getParameterName();
        $value = $this->$parameterName;

        return DataTypeFactory::getInstance()->autoDetectDataType($value);
    }
}
