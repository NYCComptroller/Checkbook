<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class PostgreSQLCreateTableStatementImpl extends AbstractCreateTableStatementImpl {

    protected function maxLength4VariableLengthString() {
        return 1024 * 1024 * 1024; // 1 GB
    }

    protected function assembleLongString(ColumnMetaData $column) {
        $column->type->databaseType = 'TEXT';
    }

    protected function assembleBigInteger(ColumnMetaData $column) {
        $column->type->databaseType = 'BIGINT';
    }

    protected function assembleColumnComment(DataSourceHandler $handler, DatasetMetaData $dataset, ColumnMetaData $column) {
        return "COMMENT ON COLUMN $dataset->source.$column->name IS " . $handler->formatStringValue($column->description);
    }

    protected function assembleTableComment(DataSourceHandler $handler, DatasetMetaData $dataset) {
        return "COMMENT ON TABLE $dataset->source IS " . $handler->formatStringValue($dataset->description);
    }

    protected function prepareCreateTableStatement(DataSourceHandler $handler, DatasetMetaData $dataset) {
        $sql = array(parent::prepareCreateTableStatement($handler, $dataset));

        foreach ($dataset->getColumns() as $column) {
            if (isset($column->description)) {
                $sql[] = $this->assembleColumnComment($handler, $dataset, $column);
            }
        }

        if (isset($dataset->description)) {
            $sql[] = $this->assembleTableComment($handler, $dataset);
        }

        return $sql;
    }
}
