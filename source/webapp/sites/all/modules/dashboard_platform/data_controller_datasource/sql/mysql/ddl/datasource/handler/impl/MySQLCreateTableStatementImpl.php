<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class MySQLCreateTableStatementImpl extends AbstractCreateTableStatementImpl {

    // can be set to NULL to use engine by default
    public static $STORAGE_ENGINE__DEFAULT = 'InnoDB';

    protected function maxLength4VariableLengthString() {
        return 65535;
    }

    protected function assembleLongString(ColumnMetaData $column) {
        $column->type->databaseType = 'TEXT';
    }

    protected function assembleBigInteger(ColumnMetaData $column) {
        $column->type->databaseType = 'BIGINT';
    }

    protected function prepareCreateTableStatement4Column(DataSourceHandler $handler, ColumnMetaData $column) {
        $sql = parent::prepareCreateTableStatement4Column($handler, $column);

        // adding column comment
        if (isset($column->description)) {
            $sql .= ' COMMENT ' . $handler->formatStringValue($column->description);
        }

        return $sql;
    }

    protected function assembleTableOptions(DataSourceHandler $handler, DatasetMetaData $dataset, $indent, &$sql) {
        parent::assembleTableOptions($handler, $dataset, $indent, $sql);

        if (isset(self::$STORAGE_ENGINE__DEFAULT)) {
            $sql .= "\nENGINE = " . self::$STORAGE_ENGINE__DEFAULT;
        }
        if (isset($dataset->description)) {
            $sql .= "\nCOMMENT = " . $handler->formatStringValue($dataset->description);
        }
    }
}
