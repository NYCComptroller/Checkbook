<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class ColumnNameHelper {

    /*
     * Splits column name into table alias and table column name
     */
    public static function splitColumnName($columnName) {
        $index = strrpos($columnName, '.');

        return ($index === FALSE)
            ? array(NULL, $columnName)
            : array(substr($columnName, 0, $index), substr($columnName, $index + 1));
    }

    /*
     * Combines table alias and table column name
     */
    public static function combineColumnName($tableAlias, $columnName) {
        return isset($tableAlias) ? $tableAlias . '.' . $columnName : $columnName;
    }
}

abstract class AbstractColumnSection extends AbstractSection {

    public $requestColumnIndex = NULL;

    abstract public function assemble($tableAlias);
}

abstract class AbstractSelectColumnSection extends AbstractColumnSection {

    public $alias = NULL;
    public $visible = TRUE;

    public function __construct($alias) {
        parent::__construct();
        $this->alias = $alias;
    }

    public function attachTo(AbstractTableSection $table) {}

    public function assemble($tableAlias) {
        $columnName = $this->assembleColumnName($tableAlias);

        return $columnName . (isset($this->alias) ? (' AS ' . $this->alias) : '');
    }

    abstract public function assembleColumnName($tableAlias);
}


class ColumnSection extends AbstractSelectColumnSection {

    public $name = NULL;
    public $key = NULL;

    // TODO in the future it should be a reference to a table which contains this column
    // This functionality is used to support table alias in GROUP BY section which has a reference to this column
    // This functionality will be deleted when we implement more comprehensive object model for SQL generation
    protected $tableAlias = NULL;

    public function __construct($name, $alias = NULL) {
        parent::__construct(isset($alias) ? $alias : $name);
        $this->name = $name;
    }

    public function event_updateTableAlias($oldTableAlias, $newTableAlias) {
        parent::event_updateTableAlias($oldTableAlias, $newTableAlias);

        if ($this->tableAlias == $oldTableAlias) {
            $this->tableAlias = $newTableAlias;
        }
    }

    public function attachTo(AbstractTableSection $table) {
        $attachedColumn = $table->findColumnByAlias($this->name);
        if (isset($attachedColumn)) {
            if (isset($this->alias)) {
                $attachedColumn->alias = $this->alias;
            }
            $attachedColumn->visible = $this->visible;
        }
        else {
            $attachedColumn = $this;

            $table->columns[] = $attachedColumn;
        }

        return $attachedColumn;
    }

    public function assemble($tableAlias) {
        return $this->assembleColumnName($tableAlias) . (($this->name === $this->alias) ? '' : (' AS ' . $this->alias));
    }

    public function assembleColumnName($tableAlias) {
        $selectedTableAlias = $this->tableAlias;

        if (isset($selectedTableAlias)) {
            if (isset($tableAlias) && ($selectedTableAlias != $tableAlias)) {
                throw new UnsupportedOperationException();
            }
        }
        else {
            $this->tableAlias = $tableAlias;
            $selectedTableAlias = $tableAlias;
        }

        return ColumnNameHelper::combineColumnName($selectedTableAlias, $this->name);
    }
}


class CompositeColumnSection extends AbstractSelectColumnSection {

    private $function = NULL;

    public function __construct($function, $alias) {
        parent::__construct($alias);
        $this->function = $function;
    }

    public function parseColumns() {
        $callback = new ColumnStatementCompositeEntityParser__ColumnNameCollector();

        $parser = new ColumnStatementCompositeEntityParser();
        $parser->parse($this->function, array($callback, 'collectColumnNames'));

        return $callback->columnNames;
    }

    public function attachTo(AbstractTableSection $table) {
        $callback = new ColumnStatementCompositeEntityParser__ColumnNameUpdater($table, FALSE);

        $parser = new ColumnStatementCompositeEntityParser();
        $this->function = $parser->parse($this->function, array($callback, 'updateColumnNames'));

        $table->columns[] = $this;
    }

    public function event_updateTableAlias($oldTableAlias, $newTableAlias) {
        parent::event_updateTableAlias($oldTableAlias, $newTableAlias);

        $callback = new ColumnStatementCompositeEntityParser__TableAliasUpdater($oldTableAlias, $newTableAlias, FALSE);

        $parser = new ColumnStatementCompositeEntityParser();
        $this->function = $parser->parse($this->function, array($callback, 'updateTableAlias'));
    }

    public function assembleColumnName($tableAlias) {
        $callback = new ColumnStatementCompositeEntityParser__TableAliasUpdater(NULL, $tableAlias, TRUE);

        $parser = new ColumnStatementCompositeEntityParser();
        return $parser->parse($this->function, array($callback, 'updateTableAlias'));
    }
}

