<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


abstract class AbstractTableSection extends AbstractSection {

    public $alias = NULL;
    public $columns = NULL;
    public $conditions = NULL;

    public function __construct($alias) {
        parent::__construct();
        $this->alias = $alias;
    }

    public function event_updateTableAlias($oldTableAlias, $newTableAlias) {
        parent::event_updateTableAlias($oldTableAlias, $newTableAlias);

        if ($oldTableAlias === $this->alias) {
            $this->alias = $newTableAlias;
        }

        if (isset($this->columns)) {
            foreach ($this->columns as $column) {
                $column->event_updateTableAlias($oldTableAlias, $newTableAlias);
            }
        }

        if (isset($this->conditions)) {
            foreach ($this->conditions as $condition) {
                $condition->event_updateTableAlias($oldTableAlias, $newTableAlias);
            }
        }
    }

    public function findColumns($columnName) {
        $columns = NULL;

        if (isset($this->columns)) {
            foreach ($this->columns as $column) {
                if ($column instanceof ColumnSection) {
                    if ($column->name === $columnName) {
                        $columns[] = $column;
                    }
                }
            }
        }

        return $columns;
    }

    public function findColumn($columnName) {
        if (isset($this->columns)) {
            foreach ($this->columns as $column) {
                if ($column instanceof ColumnSection) {
                    if ($column->name === $columnName) {
                        return $column;
                    }
                }
                elseif ($column->alias === $columnName) {
                    return $column;
                }
            }
        }

        // looking for a column by alias
        if (isset($this->columns)) {
            foreach ($this->columns as $column) {
                if ($column instanceof ColumnSection) {
                    if ($column->alias === $columnName) {
                        return $column;
                    }
                }
            }
        }

        return NULL;
    }

    public function getColumn($columnName) {
        $column = $this->findColumn($columnName);
        if (!isset($column)) {
            throw new IllegalArgumentException(t(
            	"Column '@columnName' has not been registered for the table: @tableAlias",
                array('@columnName' => $columnName, '@tableAlias' => $this->alias)));
        }

        return $column;
    }

    /**
     * @param $columnAlias
     * @return AbstractColumnSection
     */
    public function findColumnByAlias($columnAlias) {
        if (isset($this->columns)) {
            foreach ($this->columns as $column) {
                if ($column->alias === $columnAlias) {
                    return $column;
                }
            }
        }

        return NULL;
    }

    abstract public function prepareColumnTableAlias($useTableNameAsAlias);

    abstract public function assemble();
}

class TableSection extends AbstractTableSection {

    public $name = NULL;

    public function __construct($name, $alias = NULL) {
        parent::__construct($alias);
        $this->name = $name;
    }

    public function prepareColumnTableAlias($useTableNameAsAlias) {
        return isset($this->alias) ? $this->alias : ($useTableNameAsAlias ? $this->name : NULL);
    }

    protected function assembleTableName() {
        return $this->name;
    }

    public function assemble() {
        return $this->assembleTableName() . (isset($this->alias) ? (' ' . $this->alias)  : '');
    }
}

class DatasetSection extends TableSection {

    public $dataset = NULL;

    public function __construct(DatasetMetaData $dataset, $alias = NULL) {
        parent::__construct($dataset->source, $alias);
        $this->dataset = $dataset;
    }

    protected function assembleTableName() {
        $environment_metamodel = data_controller_get_environment_metamodel();

        $datasourceName = $this->dataset->datasourceName;
        $datasource = $environment_metamodel->getDataSource($datasourceName);

        $datasourceQueryHandler = DataSourceQueryFactory::getInstance()->getHandler($datasource->type);

        $owner = $datasourceQueryHandler->getDataSourceOwner($datasourceName);

        return (isset($owner) ? ($owner . '.') : '') . $this->name;
    }

    public function findColumn($columnName) {
        list($referencedDatasetName, $referencedColumnName) = ReferencePathHelper::splitReference($columnName);

        return isset($referencedDatasetName)
            ? (($this->dataset->name == $referencedDatasetName) ? parent::findColumn($referencedColumnName) : NULL)
            : parent::findColumn($referencedColumnName);
    }
}

class SubquerySection extends AbstractTableSection {

    public $body = NULL;

    public function __construct($body, $alias = NULL) {
        parent::__construct($alias);
        $this->body = $body;
    }

    public function prepareColumnTableAlias($useTableNameAsAlias) {
        $columnTableAlias = $this->alias;
        if (!isset($columnTableAlias) && $useTableNameAsAlias) {
            throw new IllegalStateException(t('Sub query is required to have an alias'));
        }

        return $columnTableAlias;
    }

    public function assemble() {
        return '(' . $this->body . ')' . (isset($this->alias) ? (' ' . $this->alias)  : '');
    }
}
