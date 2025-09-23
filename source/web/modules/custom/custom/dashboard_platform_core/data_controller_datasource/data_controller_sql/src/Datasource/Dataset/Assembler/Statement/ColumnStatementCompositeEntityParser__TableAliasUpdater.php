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

class ColumnStatementCompositeEntityParser__TableAliasUpdater extends ColumnStatementCompositeEntityParser__ColumnNameAdjuster {

    private $oldTableAlias;
    private $newTableAlias;

    public function __construct($oldTableAlias, $newTableAlias, $removeMarkerDelimiters) {
        parent::__construct($removeMarkerDelimiters);
        $this->oldTableAlias = $oldTableAlias;
        $this->newTableAlias = $newTableAlias;
    }

    public function updateTableAlias(ParserCallbackObject $callbackObject) {
        list($tableAlias, $columnName) = ColumnNameHelper::splitColumnName($callbackObject->marker);

        $updateAllowed = FALSE;
        if (isset($tableAlias)) {
            if (isset($this->oldTableAlias) && ($tableAlias === $this->oldTableAlias)) {
                $updateAllowed = TRUE;
            }
        }
        elseif (!isset($this->oldTableAlias)) {
            $updateAllowed = TRUE;
        }

        if ($updateAllowed) {
            $callbackObject->marker = ColumnNameHelper::combineColumnName($this->newTableAlias, $columnName);
            $callbackObject->markerUpdated = TRUE;
        }

        $this->adjustCallbackObject($callbackObject);
    }
}
