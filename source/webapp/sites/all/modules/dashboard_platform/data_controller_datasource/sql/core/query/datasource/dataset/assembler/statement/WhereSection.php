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


abstract class ConditionSectionValue extends AbstractSection {

    abstract public function assemble(Statement $statement, $useTableNameAsAlias);
}


class TableColumnConditionSectionValue extends ConditionSectionValue {

    public $tableAlias = NULL;
    public $columnName = NULL;

    public function __construct($tableAlias, $columnName) {
        parent::__construct();
        $this->tableAlias = $tableAlias;
        $this->columnName = $columnName;
    }

    public function event_updateTableAlias($oldTableAlias, $newTableAlias) {
        parent::event_updateTableAlias($oldTableAlias, $newTableAlias);

        if ($oldTableAlias === $this->tableAlias) {
            $this->tableAlias = $newTableAlias;
        }
    }

    public function assemble(Statement $statement, $useTableNameAsAlias) {
        $table = $statement->getTable($this->tableAlias);

        list($referencedDatasetName, $referencedColumnName) = ReferencePathHelper::splitReference($this->columnName);
        $column = $table->findColumnByAlias($referencedColumnName);

        return ' = ' . ColumnNameHelper::combineColumnName(
            $table->prepareColumnTableAlias($useTableNameAsAlias),
            (isset($column) && ($column instanceof ColumnSection)) ? $column->name : $referencedColumnName);
    }
}


class ExactConditionSectionValue extends ConditionSectionValue {

    public $value = NULL;

    public function __construct($value) {
        parent::__construct();
        $this->value = $value;
    }

    public function event_updateTableAlias($oldTableAlias, $newTableAlias) {
        parent::event_updateTableAlias($oldTableAlias, $newTableAlias);

        $callback = new ColumnStatementCompositeEntityParser__TableAliasUpdater($oldTableAlias, $newTableAlias, FALSE);

        $parser = new ColumnStatementCompositeEntityParser();
        $this->value = $parser->parse($this->value, array($callback, 'updateTableAlias'));
    }

    public function assemble(Statement $statement, $useTableNameAsAlias) {
        $callback = new ColumnStatementCompositeEntityParser__ColumnNameAdjuster(TRUE);

        $parser = new ColumnStatementCompositeEntityParser();
        return $parser->parse($this->value, array($callback, 'adjustCallbackObject'));
    }
}


abstract class AbstractConditionSection extends AbstractSection {

    public $subjectColumnName = NULL;
    public $joinValue = NULL;

    public function __construct($subjectColumnName, $joinValue) {
        parent::__construct();
        $this->subjectColumnName = $subjectColumnName;
        $this->joinValue = $joinValue;
    }

    public function event_updateTableAlias($oldTableAlias, $newTableAlias) {
        parent::event_updateTableAlias($oldTableAlias, $newTableAlias);

        $this->joinValue->event_updateTableAlias($oldTableAlias, $newTableAlias);
    }

    public function assemble(Statement $statement, AbstractTableSection $table, $useTableNameAsAlias) {
        list($referencedDatasetName, $referencedColumnName) = ReferencePathHelper::splitReference($this->subjectColumnName);

        $columnByAlias = $table->findColumnByAlias($referencedColumnName);
        $column = isset($columnByAlias) ? $table->findColumn($referencedColumnName) : NULL;

        $selectedColumn = isset($columnByAlias) && isset($column)
            ? $column
            : $columnByAlias;

        $tableAlias = $table->prepareColumnTableAlias($useTableNameAsAlias);

        return
            (isset($selectedColumn) ? $selectedColumn->assembleColumnName($tableAlias) : ColumnNameHelper::combineColumnName($tableAlias, $referencedColumnName))
                . $this->joinValue->assemble($statement, $useTableNameAsAlias);
    }
}


class JoinConditionSection extends AbstractConditionSection {}


class WhereConditionSection extends AbstractConditionSection {

    public $subjectTableAlias = NULL;

    public function __construct($subjectTableAlias, $subjectColumnName, $joinValue) {
        parent::__construct($subjectColumnName, $joinValue);
        $this->subjectTableAlias = $subjectTableAlias;
    }

    public function event_updateTableAlias($oldTableAlias, $newTableAlias) {
        if ($oldTableAlias === $this->subjectTableAlias) {
            $this->subjectTableAlias = $newTableAlias;
        }

        parent::event_updateTableAlias($oldTableAlias, $newTableAlias);
    }
}

// FIXME combine with HAVING
class CompositeWhereConditionSection extends AbstractSection {

    public $subjectTableAlias = NULL;
    public $subject = NULL;
    public $value = NULL;

    public function __construct($subjectTableAlias, CompositeColumnSection $subject, $value) {
        parent::__construct();
        $this->subjectTableAlias = $subjectTableAlias;
        $this->subject = $subject;
        $this->value = $value;
    }

    public function event_updateTableAlias($oldTableAlias, $newTableAlias) {
        if ($oldTableAlias === $this->subjectTableAlias) {
            $this->subjectTableAlias = $newTableAlias;
        }

        parent::event_updateTableAlias($oldTableAlias, $newTableAlias);

        $this->value->event_updateTableAlias($oldTableAlias, $newTableAlias);
    }

    public function assemble(Statement $statement, AbstractTableSection $table, $useTableNameAsAlias) {
        return $this->subject->assembleColumnName($table->prepareColumnTableAlias($useTableNameAsAlias))
            . $this->value->assemble($statement, $useTableNameAsAlias);
    }
}

// FIXME move to a separate file. Create parent class for AbstractConditionSection and this class
class HavingConditionSection extends AbstractSection {

    public $subject = NULL;
    public $value = NULL;

    public function __construct(CompositeColumnSection $subject, $value) {
        parent::__construct();
        $this->subject = $subject;
        $this->value = $value;
    }

    public function event_updateTableAlias($oldTableAlias, $newTableAlias) {
        parent::event_updateTableAlias($oldTableAlias, $newTableAlias);

        $this->value->event_updateTableAlias($oldTableAlias, $newTableAlias);
    }

    public function assemble(Statement $statement, $useTableNameAsAlias) {
        // FIXME statement first table is 'facts'. Try to find better way to get the table instead of using [0]
        return $this->subject->assembleColumnName($statement->tables[0]->prepareColumnTableAlias($useTableNameAsAlias))
            . $this->value->assemble($statement, $useTableNameAsAlias);
    }
}
