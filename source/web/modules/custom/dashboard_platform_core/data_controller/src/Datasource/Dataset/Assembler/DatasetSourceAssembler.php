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

namespace Drupal\data_controller\Datasource\Dataset\Assembler;

use Drupal\data_controller\Controller\CallContext\DataControllerCallContext;

interface DatasetSourceAssembler {

    // if $columnNames == NULL it means that receiving code needs ALL available columns
    // if count($columnNames) == 0 it means that receiving code does not need support for columns
    function assemble(AbstractSQLDataSourceQueryHandler $datasourceHandler, DataControllerCallContext $callcontext, DatasetMetaData $dataset, array $columnNames = NULL);
}
