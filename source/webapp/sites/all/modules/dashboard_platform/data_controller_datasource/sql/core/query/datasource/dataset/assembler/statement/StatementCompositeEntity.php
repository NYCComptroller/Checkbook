<?php
/**
* This file is part of the Checkbook NYC financial transparency software.
* 
* Copyright (C) 2012, 2013 New York City
* 
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU Affero General Public License as
* published by the Free Software Foundation, either version 3 of the
* License, or (at your option) any later version.
* 
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU Affero General Public License for more details.
* 
* You should have received a copy of the GNU Affero General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


class ColumnStatementCompositeEntityParser extends AbstractConfigurationParser {

    protected function getStartDelimiter() {
        return '$COLUMN{';
    }

    protected function getEndDelimiter() {
        return '}';
    }

    // This function is added for convenience
    public static function assembleColumnName($columnName) {
        $parser = new ColumnStatementCompositeEntityParser();

        return $parser->assemble($columnName);
    }
}


class ColumnStatementCompositeEntityParser__ColumnNameCollector extends AbstractObject {

    public $columnNames = NULL;

    public function collectColumnNames(ParserCallbackObject $callbackObject) {
        $this->columnNames[] = $callbackObject->marker;
    }
}

class ColumnStatementCompositeEntityParser__ColumnNameAdjuster extends AbstractObject {

    private $removeDelimiters;

    public function __construct($removeDelimiters) {
        parent::__construct();
        $this->removeDelimiters = $removeDelimiters;
    }

    public function adjustCallbackObject(ParserCallbackObject $callbackObject) {
        $callbackObject->removeDelimiters = $this->removeDelimiters;
    }
}

class ColumnStatementCompositeEntityParser__ColumnNameSuffixer extends ColumnStatementCompositeEntityParser__ColumnNameAdjuster {

    private $prefix = NULL;
    private $suffix = NULL;

    public function __construct($prefix, $suffix, $removeMarkerDelimiters) {
        parent::__construct($removeMarkerDelimiters);
        $this->prefix = $prefix;
        $this->suffix = $suffix;
    }

    public function updateColumnNames($callbackObject) {
        $callbackObject->marker = (isset($this->prefix) ? $this->prefix : '') . $callbackObject->marker . (isset($this->suffix) ? $this->suffix : '');
        $callbackObject->markerUpdated = TRUE;

        $this->adjustCallbackObject($callbackObject);
    }
}

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
