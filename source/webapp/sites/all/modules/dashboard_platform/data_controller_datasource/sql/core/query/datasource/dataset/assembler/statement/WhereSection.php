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

  /**
   * @param Statement $statement
   * @param $useTableNameAsAlias
   * @return mixed
   */
  abstract public function assemble(Statement $statement, $useTableNameAsAlias);
}


/**
 * Class TableColumnConditionSectionValue
 */
class TableColumnConditionSectionValue extends ConditionSectionValue {

  /**
   * @var null
   */
  public $tableAlias = NULL;
  /**
   * @var null
   */
  public $columnName = NULL;

  /**
   * TableColumnConditionSectionValue constructor.
   * @param $tableAlias
   * @param $columnName
   */
  public function __construct($tableAlias, $columnName) {
        parent::__construct();
        $this->tableAlias = $tableAlias;
        $this->columnName = $columnName;
    }

  /**
   * @param $oldTableAlias
   * @param $newTableAlias
   */
  public function event_updateTableAlias($oldTableAlias, $newTableAlias) {
        parent::event_updateTableAlias($oldTableAlias, $newTableAlias);

        if ($oldTableAlias === $this->tableAlias) {
            $this->tableAlias = $newTableAlias;
        }
    }

  /**
   * @param Statement $statement
   * @param $useTableNameAsAlias
   * @return mixed|string
   * @throws IllegalArgumentException
   */
  public function assemble(Statement $statement, $useTableNameAsAlias) {
        $table = $statement->getTable($this->tableAlias);

        list($referencedDatasetName, $referencedColumnName) = ReferencePathHelper::splitReference($this->columnName);
        $column = $table->findColumnByAlias($referencedColumnName);

        return ' = ' . ColumnNameHelper::combineColumnName(
            $table->prepareColumnTableAlias($useTableNameAsAlias),
            (isset($column) && ($column instanceof ColumnSection)) ? $column->name : $referencedColumnName);
    }
}


/**
 * Class ExactConditionSectionValue
 */
class ExactConditionSectionValue extends ConditionSectionValue {

  /**
   * @var null
   */
  public $value = NULL;
  /**
   * @var bool
   */
  public $stringOperation = false;

  /**
   * ExactConditionSectionValue constructor.
   * @param $value
   * @param bool $propertyValue
   */
  public function __construct($value, $propertyValue = false) {
        parent::__construct();
        $this->value = $value;
        if ($propertyValue && $propertyValue instanceof RegularExpressionOperatorHandler){
          $this->stringOperation = true;
        }
    }

  /**
   * @param $oldTableAlias
   * @param $newTableAlias
   * @throws UnsupportedOperationException
   */
  public function event_updateTableAlias($oldTableAlias, $newTableAlias) {
        parent::event_updateTableAlias($oldTableAlias, $newTableAlias);

        $callback = new ColumnStatementCompositeEntityParser__TableAliasUpdater($oldTableAlias, $newTableAlias, FALSE);

        $parser = new ColumnStatementCompositeEntityParser();
        $this->value = $parser->parse($this->value, array($callback, 'updateTableAlias'));
    }

  /**
   * @param Statement $statement
   * @param $useTableNameAsAlias
   * @return mixed
   * @throws UnsupportedOperationException
   */
  public function assemble(Statement $statement, $useTableNameAsAlias) {
        $callback = new ColumnStatementCompositeEntityParser__ColumnNameAdjuster(TRUE);

        $parser = new ColumnStatementCompositeEntityParser();
        return $parser->parse($this->value, array($callback, 'adjustCallbackObject'));
    }
}


/**
 * Class AbstractConditionSection
 */
abstract class AbstractConditionSection extends AbstractSection {

  /**
   * @var null
   */
  public $subjectColumnName = NULL;
  /**
   * @var null
   */
  public $joinValue = NULL;

  /**
   * AbstractConditionSection constructor.
   * @param $subjectColumnName
   * @param $joinValue
   */
  public function __construct($subjectColumnName, $joinValue) {
        parent::__construct();
        $this->subjectColumnName = $subjectColumnName;
        $this->joinValue = $joinValue;
    }

  /**
   * @param $oldTableAlias
   * @param $newTableAlias
   */
  public function event_updateTableAlias($oldTableAlias, $newTableAlias) {
        parent::event_updateTableAlias($oldTableAlias, $newTableAlias);

        $this->joinValue->event_updateTableAlias($oldTableAlias, $newTableAlias);
    }

  /**
   * @param Statement $statement
   * @param AbstractTableSection $table
   * @param $useTableNameAsAlias
   * @return string
   */
  public function assemble(Statement $statement, AbstractTableSection $table, $useTableNameAsAlias) {
        list($referencedDatasetName, $referencedColumnName) = ReferencePathHelper::splitReference($this->subjectColumnName);

        $columnByAlias = $table->findColumnByAlias($referencedColumnName);
        $column = isset($columnByAlias) ? $table->findColumn($referencedColumnName) : NULL;

        $selectedColumn = isset($columnByAlias) && isset($column)
            ? $column
            : $columnByAlias;

        $tableAlias = $table->prepareColumnTableAlias($useTableNameAsAlias);

        // We need to cast integer DB fields for autocomplete
        // CAST(s0_b.release_approved_year AS text)  ~* '(.* 201.*)|(^201.*)'
        $sql_column = isset($selectedColumn) ? $selectedColumn->assembleColumnName($tableAlias) : ColumnNameHelper::combineColumnName($tableAlias, $referencedColumnName);
        if (('integer' === $this->subjectColumnType??false) && ($this->joinValue->stringOperation??false)) {
          $sql_column = " CAST({$sql_column} AS text) ";
        }

        return
          $sql_column
                . $this->joinValue->assemble($statement, $useTableNameAsAlias);
    }
}


/**
 * Class JoinConditionSection
 */
class JoinConditionSection extends AbstractConditionSection {}


/**
 * Class WhereConditionSection
 */
class WhereConditionSection extends AbstractConditionSection {

  /**
   * @var null
   */
  public $subjectTableAlias = NULL;
  /**
   * @var null
   */
  public $subjectColumnType = null;

  /**
   * WhereConditionSection constructor.
   * @param $subjectTableAlias
   * @param $subjectColumnName
   * @param $joinValue
   * @param null $subjectColumnType
   */
  public function __construct($subjectTableAlias, $subjectColumnName, $joinValue, $subjectColumnType = null) {
        parent::__construct($subjectColumnName, $joinValue);
        $this->subjectTableAlias = $subjectTableAlias;
        $this->subjectColumnType = $subjectColumnType;
    }

  /**
   * @param $oldTableAlias
   * @param $newTableAlias
   */
  public function event_updateTableAlias($oldTableAlias, $newTableAlias) {
        if ($oldTableAlias === $this->subjectTableAlias) {
            $this->subjectTableAlias = $newTableAlias;
        }

        parent::event_updateTableAlias($oldTableAlias, $newTableAlias);
    }
}

// FIXME combine with HAVING

/**
 * Class CompositeWhereConditionSection
 */
class CompositeWhereConditionSection extends AbstractSection {

  /**
   * @var null
   */
  public $subjectTableAlias = NULL;
  /**
   * @var CompositeColumnSection|null
   */
  public $subject = NULL;
  /**
   * @var null
   */
  public $value = NULL;

  /**
   * CompositeWhereConditionSection constructor.
   * @param $subjectTableAlias
   * @param CompositeColumnSection $subject
   * @param $value
   */
  public function __construct($subjectTableAlias, CompositeColumnSection $subject, $value) {
        parent::__construct();
        $this->subjectTableAlias = $subjectTableAlias;
        $this->subject = $subject;
        $this->value = $value;
    }

  /**
   * @param $oldTableAlias
   * @param $newTableAlias
   */
  public function event_updateTableAlias($oldTableAlias, $newTableAlias) {
        if ($oldTableAlias === $this->subjectTableAlias) {
            $this->subjectTableAlias = $newTableAlias;
        }

        parent::event_updateTableAlias($oldTableAlias, $newTableAlias);

        $this->value->event_updateTableAlias($oldTableAlias, $newTableAlias);
    }

  /**
   * @param Statement $statement
   * @param AbstractTableSection $table
   * @param $useTableNameAsAlias
   * @return string
   */
  public function assemble(Statement $statement, AbstractTableSection $table, $useTableNameAsAlias) {
        return $this->subject->assembleColumnName($table->prepareColumnTableAlias($useTableNameAsAlias))
            . $this->value->assemble($statement, $useTableNameAsAlias);
    }
}

// FIXME move to a separate file. Create parent class for AbstractConditionSection and this class

/**
 * Class HavingConditionSection
 */
class HavingConditionSection extends AbstractSection {

  /**
   * @var CompositeColumnSection|null
   */
  public $subject = NULL;
  /**
   * @var null
   */
  public $value = NULL;

  /**
   * HavingConditionSection constructor.
   * @param CompositeColumnSection $subject
   * @param $value
   */
  public function __construct(CompositeColumnSection $subject, $value) {
        parent::__construct();
        $this->subject = $subject;
        $this->value = $value;
    }

  /**
   * @param $oldTableAlias
   * @param $newTableAlias
   */
  public function event_updateTableAlias($oldTableAlias, $newTableAlias) {
        parent::event_updateTableAlias($oldTableAlias, $newTableAlias);

        $this->value->event_updateTableAlias($oldTableAlias, $newTableAlias);
    }

  /**
   * @param Statement $statement
   * @param $useTableNameAsAlias
   * @return string
   */
  public function assemble(Statement $statement, $useTableNameAsAlias) {
        // FIXME statement first table is 'facts'. Try to find better way to get the table instead of using [0]
        return $this->subject->assembleColumnName($statement->tables[0]->prepareColumnTableAlias($useTableNameAsAlias))
            . $this->value->assemble($statement, $useTableNameAsAlias);
    }
}
