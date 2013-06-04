<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
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
