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

use Drupal\data_controller\Common\Datatype\Handler\StringDataTypeHandler;
use Drupal\data_controller\Common\Object\Manipulation\StringHelper;
use Drupal\data_controller\Datasource\Operator\ParameterBasedOperatorHandler;


abstract class AbstractWildcardOperatorHandler extends AbstractOperatorHandler implements ParameterBasedOperatorHandler {

    public $wildcard = NULL;
    public $anyCharactersOnLeft = FALSE;
    public $anyCharactersOnRight = FALSE;

    public function __construct($configuration, $wildcard, $anyCharactersOnLeft = FALSE, $anyCharactersOnRight = FALSE) {
        parent::__construct($configuration);

        $this->wildcard = StringHelper::trim($wildcard);
        $this->anyCharactersOnLeft = $anyCharactersOnLeft;
        $this->anyCharactersOnRight = $anyCharactersOnRight;
    }

    public function getParameterDataType() {
        return StringDataTypeHandler::$DATA_TYPE;
    }
}
