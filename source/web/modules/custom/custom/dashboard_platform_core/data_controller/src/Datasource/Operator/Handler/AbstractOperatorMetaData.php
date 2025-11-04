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

use Drupal\data_controller\Common\Object\Exception\IllegalArgumentException;
use Drupal\data_controller\Common\Object\Exception\IllegalStateException;
use Drupal\data_controller\Common\Pattern\AbstractObject;
use Drupal\data_controller\Datasource\Operator\OperatorMetaData;
use Drupal\data_controller\Datasource\Operator\OperatorParameter;

abstract class AbstractOperatorMetaData extends AbstractObject implements OperatorMetaData {

    protected $parameters = NULL;

    public function __construct() {
        parent::__construct();
        $parameters = $this->initiateParameters();
        $this->checkParameters($parameters);

        $this->parameters = $parameters;
    }

    abstract protected function initiateParameters();

    protected function checkParameters(array $parameters = NULL) {
        if (!isset($parameters)) {
            return;
        }

        // optional parameters have to be at the end of the list
        $optionalParameterFound = FALSE;
        foreach ($parameters as $parameter) {
            if ($parameter->required) {
                if ($optionalParameterFound) {
                    throw new IllegalStateException(t(
                        "Optional parameter '@parameterName' cannot be placed before any required parameters",
                        array('@parameterName' => $parameter->name)));
                }
            }
            else {
                $optionalParameterFound = TRUE;
            }
        }
    }

    /**
     * @return OperatorParameter[]
     */
    public function getParameters() {
        return $this->parameters;
    }

    public function getParameterCount() {
        return count($this->parameters);
    }

    public function getRequiredParameterCount() {
        $requiredParameterCount = 0;

        if (isset($this->parameters)) {
            foreach ($this->parameters as $parameter) {
                if ($parameter->required) {
                    $requiredParameterCount++;
                }
            }
        }

        return $requiredParameterCount;
    }

    public function checkParameterName($parameterName) {
        $supportedParameters = NULL;

        if (isset($this->parameters)) {
            foreach ($this->parameters as $parameter) {
                if ($parameter->name === $parameterName) {
                    return;
                }

                $supportedParameters[] = $parameter->name;
            }
        }

        throw new IllegalArgumentException(t(
        	"'@parameterName' parameter is not supported by the operator.@supportedParametersIfAny",
            array(
            	'@parameterName' => $parameterName,
            	'@supportedParametersIfAny' =>
                    (isset($supportedParameters)
                        ? t(' Supported parameters: [@supportedParameters]', array('@supportedParameters' => implode(', ', $supportedParameters)))
                        : t(' No parameters are supported')))));
    }
}
