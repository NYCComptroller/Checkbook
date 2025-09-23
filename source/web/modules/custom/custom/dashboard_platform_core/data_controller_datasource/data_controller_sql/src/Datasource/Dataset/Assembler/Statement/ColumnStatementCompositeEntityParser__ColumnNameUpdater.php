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

namespace Drupal\data_controller_sql\Datasource\Dataset\Assembler\Statement;

use Drupal\data_controller\Common\Object\Parser\ParserCallbackObject;

class ColumnStatementCompositeEntityParser__ColumnNameUpdater extends ColumnStatementCompositeEntityParser__ColumnNameAdjuster {

    private $table;

    public function __construct(AbstractTableSection $table, $removeMarkerDelimiters) {
        parent::__construct($removeMarkerDelimiters);
        $this->table = $table;
    }

    public function updateColumnNames(ParserCallbackObject $callbackObject) {
        list($tableAlias, $columnName) = ColumnNameHelper::splitColumnName($callbackObject->marker);

        $column = $this->table->findColumnByAlias($columnName);
        if (isset($column)) {
            $callbackObject->marker = ColumnNameHelper::combineColumnName($tableAlias, $column->name);
            $callbackObject->markerUpdated = TRUE;
        }

        $this->adjustCallbackObject($callbackObject);
    }
}
