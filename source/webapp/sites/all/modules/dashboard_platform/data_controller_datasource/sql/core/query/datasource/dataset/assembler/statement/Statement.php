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




class AssembledSections extends AbstractObject {

    public $select = NULL;
    public $from = NULL;
    public $where = NULL;
    public $groupBy = NULL;
    public $having = NULL;
    public $logicalOrColumns = NULL;

    public function __construct($select, $from, $where, $groupBy, $having, $logicalOrColumns = null) {
        parent::__construct();
        $this->select = $select;
        $this->from = $from;
        $this->where = $where;
        $this->groupBy = $groupBy;
        $this->having = $having;
        $this->logicalOrColumns = $logicalOrColumns;
    }
}


class Statement extends AbstractObject {

    private static $TABLE_ALIAS__SOURCE = 'a';

    public static $INDENT_INSERT_VALUES = 5;

    public static $INDENT_NESTED = 4;
    public static $INDENT_SELECT_SECTION_ELEMENT = 7; // 'SELECT '
    public static $INDENT_SUBQUERY = 8; // '(SELECT '
    public static $INDENT_LEFT_OUTER_JOIN_SUBQUERY = 24; // LEFT OUTER JOIN (SELECT

    public $tables = NULL;
    public $conditions = NULL;
    public $groupByColumns = NULL;
    public $havingConditions = NULL;
    public $logicalOrColumns = NULL;

    public function merge(Statement $statement) {
        ArrayHelper::mergeArrays($this->tables, $statement->tables);
        ArrayHelper::mergeArrays($this->conditions, $statement->conditions);
        ArrayHelper::mergeArrays($this->groupByColumns, $statement->groupByColumns);
        ArrayHelper::mergeArrays($this->havingConditions, $statement->havingConditions);
        ArrayHelper::mergeArrays($this->logicalOrColumns, $statement->logicalOrColumns);
    }

    public function addTableAliasPrefix($prefix) {
        if (isset($this->tables)) {
            foreach ($this->tables as $table) {
                $oldTableAlias = $table->alias;
                $newTableAlias = $prefix . (isset($oldTableAlias) ? '_' . $oldTableAlias : '');

                $this->updateTableAlias($oldTableAlias, $newTableAlias);
            }
        }
    }

    public function updateTableAlias($oldTableAlias, $newTableAlias) {
        if ($oldTableAlias === $newTableAlias) {
            return;
        }

        if (isset($this->tables)) {
            foreach ($this->tables as $table) {
                $table->event_updateTableAlias($oldTableAlias, $newTableAlias);
            }
        }
        if (isset($this->conditions)) {
            foreach ($this->conditions as $condition) {
                $condition->event_updateTableAlias($oldTableAlias, $newTableAlias);
            }
        }
        if (isset($this->groupByColumns)) {
            foreach ($this->groupByColumns as $groupByColumn) {
                $groupByColumn->event_updateTableAlias($oldTableAlias, $newTableAlias);
            }
        }
        if (isset($this->havingConditions)) {
            foreach ($this->havingConditions as $condition) {
                $condition->event_updateTableAlias($oldTableAlias, $newTableAlias);
            }
        }
        if (isset($this->logicalOrColumns)) {
            foreach ($this->logicalOrColumns as $logicalOrColumns) {
                $logicalOrColumns->event_updateTableAlias($oldTableAlias, $newTableAlias);
            }
        }
    }

    public function getTable($tableAlias) {
        if (isset($this->tables)) {
            foreach ($this->tables as $table) {
                if ($table->alias === $tableAlias) {
                    return $table;
                }
            }
        }

        LogHelper::log_error($this);
        throw new IllegalArgumentException(t('Could not find table by the alias: @tableAlias', array('@tableAlias' => $tableAlias)));
    }

    public function findColumnTable($columnName, $visibleOnly = FALSE) {
        // if we have only one table for the statement we do not check if the column is available or not.
        // We do not have any other choice. There is no table left to support the column.
        // If column is not correct SQL statement will fail during execution
        if (count($this->tables) === 1) {
            return $this->tables[0];
        }

        $exactKeyedMatchSourceTables = NULL;
        $exactMatchSourceTables = NULL;
        $potentialMatchSourceTables = NULL;
        foreach ($this->tables as $table) {
            if (isset($table->columns)) {
                $columnByAlias = $table->findColumnByAlias($columnName);
                if ($visibleOnly && isset($columnByAlias) && !$columnByAlias->visible) {
                    $columnByAlias = NULL;
                }

                $column = $table->findColumn($columnName);
                if ($visibleOnly && isset($column) && !$column->visible) {
                    $column = NULL;
                }

                $selectedColumn = $column;
                if (isset($column)) {
                    if (isset($columnByAlias)) {
                        if (isset($columnByAlias->key) && $columnByAlias->key) {
                            $selectedColumn = $columnByAlias;
                        }
                    }
                }
                else {
                    $selectedColumn = $columnByAlias;
                }

                if (isset($selectedColumn)) {
                    if (isset($selectedColumn->key) && $selectedColumn->key) {
                        $exactKeyedMatchSourceTables[] = $table;
                    }
                    else {
                        $exactMatchSourceTables[] = $table;
                    }
                }
            }
            else {
                $potentialMatchSourceTables[] = $table;
            }
        }

        $selectedTable = NULL;

        $exactKeyedMatchCount = !empty($exactKeyedMatchSourceTables) ? count($exactKeyedMatchSourceTables) : 0;
        if ($exactKeyedMatchCount === 0) {
            $exactMatchCount = !empty($exactMatchSourceTables) ? count($exactMatchSourceTables) : 0;
            if ($exactMatchCount === 0) {
                if (count($potentialMatchSourceTables) === 1) {
                    $selectedTable = $potentialMatchSourceTables[0];
                }
            }
            elseif ($exactMatchCount === 1) {
                $selectedTable = $exactMatchSourceTables[0];
            }
            else {
                // selecting first for now
                $selectedTable = $exactMatchSourceTables[0];
            }
        }
        elseif ($exactKeyedMatchCount === 1) {
            $selectedTable = $exactKeyedMatchSourceTables[0];
        }
        else {
            // selecting first for now
            $selectedTable = $exactKeyedMatchSourceTables[0];
        }

        return $selectedTable;
    }

    public function getColumnTable($columnName, $visibleOnly = FALSE) {
        $table = $this->findColumnTable($columnName, $visibleOnly);
        if (!isset($table)) {
            LogHelper::log_error($this);
            throw new IllegalArgumentException(t("Could not identify '@columnName' column in this statement", array('@columnName' => $columnName)));
        }

        return $table;
    }

    public function prepareSections(array $requestedColumnNames = NULL) {
        $retrieveAllColumns = !isset($requestedColumnNames);

        $tableCount = count($this->tables);
        if ($tableCount === 0) {
            throw new IllegalStateException(t('Tables have not been defined for this statement'));
        }

        if ($tableCount === 1) {
            $table = $this->tables[0];
            if ($table instanceof SubquerySection) {
                if (!isset($table->columns) && !isset($this->conditions) && !isset($this->groupByColumns) && !isset($this->havingConditions)) {
                    return array(TRUE, new AssembledSections(NULL, $table->body, NULL, NULL, NULL));
                }
            }
        }

        // we need to find each column in order to avoid using sub select
        $isColumnAccessible = TRUE;

        $columnNameUsage = NULL;
        if (!$retrieveAllColumns) {
            // preparing tables which support each requested column
            foreach ($requestedColumnNames as $columnName) {
                $columnNameUsage[$columnName] = [];
            }

            // preparing tables which support 'where' subject columns for which we did not select table alias
            if (isset($this->conditions)) {
                foreach ($this->conditions as $condition) {
                    if (!isset($condition->subjectTableAlias)) {
                        $columnNameUsage[$condition->subjectColumnName] = [];
                    }
                }
            }
        }

        // if the statement contains just one table we do not need to worry about columns
        if (!$retrieveAllColumns) {
            if (($tableCount === 1) && (($table = $this->tables[0]) instanceof TableSection)) {
                if (isset($columnNameUsage)) {
                    foreach ($columnNameUsage as &$usage) {
                        $usage[] = $table;
                    }
                }
            }
            else {
                // calculating how many tables support each column
                for ($i = 0; ($i < $tableCount) && $isColumnAccessible; $i++) {
                    $table = $this->tables[$i];
                    if (isset($table->columns)) {
                        foreach ($table->columns as $column) {
                            if (isset($columnNameUsage[$column->alias])) {
                                $columnNameUsage[$column->alias][] = $table;
                            }
                        }
                    }
                    else {
                        if ($table instanceof TableSection) {
                            // list of columns is not provided for the table
                            // I hope that is because the table is just transitioning table to access data from other table
                            // I do not want to use $isColumnAccessible = FALSE until we have a use case
                        }
                        else {
                            // there is no access to columns for this type of table
                            $isColumnAccessible = FALSE;
                        }
                    }
                }

                // checking how many tables support each column
                if (isset($columnNameUsage)) {
                    foreach ($columnNameUsage as $sourceTables) {
                        if (count($sourceTables) !== 1) {
                            $isColumnAccessible = FALSE;
                            break;
                        }
                    }
                }
            }
        }

        $useTableNameAsAlias = $tableCount > 1;

        $indentSelectSectionElement = str_pad('', self::$INDENT_SELECT_SECTION_ELEMENT);

        $indexedSelect = $unindexedSelect = NULL;
        if ($isColumnAccessible && !$retrieveAllColumns) {
            // all columns are linked to corresponding tables
            foreach ($requestedColumnNames as $columnName) {
                $sourceTable = $columnNameUsage[$columnName][0];
                $column = $sourceTable->findColumnByAlias($columnName);
                if (isset($column) && !$column->visible) {
                    continue;
                }

                $columnTableAlias = $sourceTable->prepareColumnTableAlias($useTableNameAsAlias);

                // if there is no such columns we return just requested column name
                if (isset($column)) {
                    $assembledColumn = $column->assemble($columnTableAlias);
                    if (isset($column->requestColumnIndex)) {
                        $indexedSelect[$column->requestColumnIndex][] = $assembledColumn;
                    }
                    else {
                        $unindexedSelect[] = $assembledColumn;
                    }
                }
                else {
                    $unindexedSelect[] = ColumnNameHelper::combineColumnName($columnTableAlias, $columnName);
                }
            }
        }
        else {
            foreach ($this->tables as $table) {
                // we ignore a table which provides list of columns but the list is empty
                if (isset($table->columns) && (count($table->columns) === 0)) {
                    continue;
                }

                $columnTableAlias = $table->prepareColumnTableAlias($useTableNameAsAlias);
                if (isset($table->columns)) {
                    // returning only columns which are configured for the table
                    foreach ($table->columns as $column) {
                        if (!$column->visible) {
                            continue;
                        }

                        $assembledColumn = $column->assemble($columnTableAlias);
                        if (isset($column->requestColumnIndex)) {
                            $indexedSelect[$column->requestColumnIndex][] = $assembledColumn;
                        }
                        else {
                            $unindexedSelect[] = $assembledColumn;
                        }
                    }
                }
                else {
                    // returning all columns from the table
                    $unindexedSelect[] = ColumnNameHelper::combineColumnName($columnTableAlias, '*');
                }
            }
        }
        // sorting select columns by request column index. If the index is not provided, corresponding column is placed at the end of the list
        $sortedSelect = [];
        if (isset($indexedSelect)) {
            ksort($indexedSelect);
            foreach ($indexedSelect as $assembledColumns) {
                $sortedSelect = array_merge($sortedSelect, $assembledColumns);
            }
        }
        if (isset($unindexedSelect)) {
            $sortedSelect= array_merge($sortedSelect, $unindexedSelect);
        }
        $select = (count($sortedSelect) > 0) ? implode(",\n$indentSelectSectionElement", $sortedSelect) : NULL;

        $from = NULL;
        for ($i = 0; $i < $tableCount; $i++) {
            $table = $this->tables[$i];
            if ($i > 0) {
                $from .= "\n" . $indentSelectSectionElement . 'LEFT OUTER JOIN ';
            }
            $from .= $table->assemble();
            // assembling join conditions
            if ($i === 0) {
                if (isset($table->conditions)) {
                    throw new IllegalStateException(t("Join conditions should not be defined for '@tableName' table", array('@tableName' => $table->name)));
                }
            }
            else {
                if (isset($table->conditions)) {
                    $from .= ' ON ';
                    for ($j = 0, $c = count($table->conditions); $j < $c; $j++) {
                        $condition = $table->conditions[$j];
                        if ($j > 0) {
                            $from .= ' AND ';
                        }

                        $from .= $condition->assemble($this, $table, $useTableNameAsAlias);
                    }
                }
                else {
                    throw new IllegalStateException(t("Join conditions were not defined for '@tableName' table", array('@tableName' => $table->name)));
                }
            }
        }

        $where = NULL;
        $whereOrPart = NULL;
        $whereAndPart = NULL;
        if (isset($this->conditions)) {
            if (!isset($this->logicalOrColumns)) {
                foreach ($this->conditions as $condition) {
                    $where .= isset($where) ? "\n   AND " : "";
                    $where = self::assembleWhere($condition,$useTableNameAsAlias, $where);
                }
            }
            //Update the where clause to handle OR condition
            else {
                if(isset($this->logicalOrColumns)) {

                    $arrayAllOrConditions = [];

                    foreach ($this->logicalOrColumns as $logicalAllOrColumnsArray) {

                        $arrayOrConditions = [];
                        foreach($this->conditions as $condition) {
                            $isOrCondition = false;
                            foreach ($logicalAllOrColumnsArray as $logicalOrColumn) {
                                if ($condition->subjectColumnName == $logicalOrColumn) {
                                    $isOrCondition = true;
                                    break;
                                }
                            }
                            if ($isOrCondition) {
                                $arrayOrConditions[] = $condition;
                                $arrayExcludeColumns[] = $condition->subjectColumnName;
                            }
                        }
                        //If arrayOrConditions has multiple columns, use the OR, else AND
                        if(count($arrayOrConditions) > 1) {
                            $arrayAllOrConditions[] = $arrayOrConditions;
                        }
                        //Remove from exclude list if AND is used
                        elseif(count($arrayOrConditions) == 1) {
                            foreach($arrayExcludeColumns as $key => $value) {
                                if($value == $arrayOrConditions[0]->subjectColumnName) {
                                    unset($arrayExcludeColumns[$key]);
                                }
                            }
                        }
                    }

                    //Build the array for the AND columns, removing the OR columns
                    $arrayAndConditions = [];
                    if(isset($arrayExcludeColumns)) {
                        foreach($this->conditions as $condition) {
                            if (array_search($condition->subjectColumnName, $arrayExcludeColumns) === false) {
                                $arrayAndConditions[] = $condition;
                            }
                        }
                    }
                    else {
                        $arrayAndConditions = $this->conditions;
                    }

                    //Build where using OR for arrayOrConditions
                    $whereOrPart = null;
                    if(count($arrayAllOrConditions) > 0) {
                        foreach($arrayAllOrConditions as $arrayOrConditions)   {
                            $whereOrSubPart = null;
                            $whereOrPart .= isset($whereOrPart) ? " AND " : "";
                            foreach($arrayOrConditions as $index => $condition) {
                                $whereOrSubPart .= isset($whereOrSubPart) ? " OR " : "(";
                                $whereOrSubPart = self::assembleWhere($condition,$useTableNameAsAlias, $whereOrSubPart);
                                $whereOrSubPart .= ($index == count($arrayOrConditions) - 1) ? ")" : "";
                            }
                            $whereOrPart .= $whereOrSubPart;
                        }
                    }
                    //Build where using AND for arrayAndConditions
                    if(isset($arrayAndConditions)) {
                        foreach($arrayAndConditions as $condition) {
                            $whereAndPart .= isset($whereAndPart) ? "\n   AND " : "";
                            $whereAndPart = self::assembleWhere($condition,$useTableNameAsAlias, $whereAndPart);
                        }
                        $whereAndPart = $whereAndPart == "" ? null : $whereAndPart;
                    }
                    if (isset($whereAndPart)) {
                        $where = $whereAndPart;
                    }
                    if (isset($whereOrPart)) {
                        $where = isset($where) ? "{$where} \n   AND {$whereOrPart}" : $whereOrPart;
                    }
                }
            }
        }

        $groupBy = NULL;
        if (isset($this->groupByColumns)) {
            foreach ($this->groupByColumns as $groupByColumn) {
                if (isset($groupBy)) {
                    $groupBy .= ', ';
                }
                $groupBy .= $groupByColumn->assemble(NULL);
            }
        }

        $having = NULL;
        if (isset($this->havingConditions)) {
            foreach ($this->havingConditions as $havingCondition) {
                if (isset($having)) {
                    $having .= "\n   AND ";
                }
                $having .= $havingCondition->assemble($this, FALSE);
            }
        }

        $isSubqueryRequired = !$isColumnAccessible;

        return array($isSubqueryRequired, new AssembledSections($select, $from, $where, $groupBy, $having));
    }


    private function assembleWhere($condition, $useTableNameAsAlias, $where) {

        if (isset($condition->subjectTableAlias)) {
            $subjectTable = $this->getTable($condition->subjectTableAlias);
        }
        else {
            // We do not have table alias
            // Solution: find a column in a table and use the table alias and the column name to generate the condition
            if (isset($columnNameUsage)) {
                $sourceTables = $columnNameUsage[$condition->subjectColumnName];
                $subjectTable = (count($sourceTables) === 1) ? $sourceTables[0] : NULL;
            }
            else {
                $subjectTable = $this->findColumnTable($condition->subjectColumnName);
            }
            if (!isset($subjectTable)) {
                throw new IllegalStateException(t(
                    "Condition for column '@columnName' cannot be prepared",
                    array('@columnName' => $condition->subjectColumnName)));
            }
        }

        $where .= $condition->assemble($this, $subjectTable, $useTableNameAsAlias);
        return $where;
    }

    public static function assemble($isSubqueryRequired, array $columnNames = NULL, AssembledSections $assembledSections = NULL, $indent = 0, $indentBlockStart = TRUE) {
        if (isset($assembledSections->select)) {
            $sql = "SELECT $assembledSections->select"
                . "\n  FROM $assembledSections->from"
                . (isset($assembledSections->where) ? "\n WHERE $assembledSections->where" : '')
                . (isset($assembledSections->groupBy) ? "\n GROUP BY $assembledSections->groupBy" : '')
                . (isset($assembledSections->having) ? "\nHAVING $assembledSections->having" : '');
        }
        else {
            if (isset($assembledSections->where) || isset($assembledSections->groupBy) || isset($assembledSections->having)) {
                throw new UnsupportedOperationException(t("Additional sections could not be added to assembled SQL statement"));
            }

            $sql = $assembledSections->from;
        }

        if ($isSubqueryRequired) {
            $assembledSubquerySections = new AssembledSections(
                self::$TABLE_ALIAS__SOURCE . '.' . (isset($columnNames) ? implode(', ' . self::$TABLE_ALIAS__SOURCE . '.', $columnNames) : '*'),
                '(' . StringHelper::indent($sql, self::$INDENT_SUBQUERY, FALSE) . ') ' . self::$TABLE_ALIAS__SOURCE,
                NULL,
                NULL,
                NULL);
            $sql = self::assemble(FALSE, NULL, $assembledSubquerySections, $indent, $indentBlockStart);
        }
        else {
            $sql = StringHelper::indent($sql, $indent, $indentBlockStart);
        }

        return $sql;
    }
}
