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
use Drupal\data_controller\Common\Pattern\AbstractObject;
use Drupal\data_controller\Datasource\Operator\OperatorHandler;

abstract class AbstractOperatorHandler extends AbstractObject implements OperatorHandler {

    public $metadata = NULL;
    public $weight = NULL;

    protected $calculatedValues = NULL;
    protected $calculatedValueFlags = NULL; // we need the flags because calculated values could be NULL

    public function __construct($metadata) {
        parent::__construct();
        $this->metadata = $metadata;
    }

    public function __clone() {
        parent::__clone();

        // when we clone this object we need to clean precalculated values
        // usually clonning is done before  modification of this or parent objects and that could lead to different calculation
        $this->calculatedValues = NULL;
        $this->calculatedValueFlags = NULL;
    }

    public function isSubsetBased() {
        return FALSE;
    }

    public function wasValueCalculated($variableName) {
        return isset($this->calculatedValueFlags[$variableName]);
    }

    public function getCalculatedValue($variableName) {
        if (!$this->wasValueCalculated($variableName)) {
            throw new IllegalArgumentException("'@variableName' variable has not been calculated", array('@variableName' => $variableName));
        }

        return isset($this->calculatedValues[$variableName]) ? $this->calculatedValues[$variableName] : NULL;
    }

    public function setCalculatedValue($variableName, $value) {
        $this->calculatedValues[$variableName] = $value;
        $this->calculatedValueFlags[$variableName] = TRUE;
    }
}
