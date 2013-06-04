<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class PostgreSQLInitializeConnectionImpl extends AbstractInitializePDOConnectionImpl {

    public function initializePDOConnection(DataSourceHandler $handler, DataSourceMetaData $datasource) {
        $dsn = "pgsql:host=$datasource->host";
        if (isset($datasource->port)) {
            $dsn .= ";port=$datasource->port";
        }
        if (isset($datasource->database)) {
            $dsn .= ";dbname=$datasource->database";
        }

        if (!extension_loaded('pdo_pgsql')) {
            throw new IllegalStateException(t("'PostgreSQL PDO' PHP extension is not loaded"));
        }

        return new PDO($dsn, $datasource->username, $datasource->password);
    }
}
