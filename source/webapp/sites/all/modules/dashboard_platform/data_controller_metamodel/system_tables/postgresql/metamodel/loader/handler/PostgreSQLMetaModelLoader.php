<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class PostgreSQLMetaModelLoader extends AbstractSystemTableMetaModelLoader {

    protected function selectedDataSourceType() {
        return PostgreSQLDataSource::TYPE;
    }

    protected function prepareColumnsMetaDataProperties(DataSourceMetaData $datasource, array $tableNames) {
        $datasourceHandler = DataSourceQueryFactory::getInstance()->getHandler($datasource->type);

        $sql = 'SELECT c.relname AS ' . self::PROPERTY__TABLE_NAME . ', '
             . '       a.attname AS ' . self::PROPERTY__COLUMN_NAME . ', '
             . '       a.attnum AS ' . self::PROPERTY__COLUMN_INDEX . ', '
             . '       t.typname AS ' . self::PROPERTY__COLUMN_TYPE
             . '  FROM pg_class c INNER JOIN pg_namespace ns ON ns.oid = c.relnamespace'
             . '       INNER JOIN pg_attribute a ON a.attrelid = c.oid'
             . '       INNER JOIN pg_type t ON t.oid = a.atttypid'
             . " WHERE c.relname IN ('" .  implode("', '", $tableNames) . "')"
             . "   AND c.relkind IN ('r','v')"
             . "   AND ns.nspname = '$datasource->schema'"
             . '   AND a.attnum > 0';

        LogHelper::log_info(new StatementLogMessage('metadata.dataset.systemTable', $sql));

        return $datasourceHandler->executeQuery(new DataControllerCallContext(), $datasource, $sql, new PassthroughResultFormatter());
    }
}
