<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class OracleCreateDatabaseStatementImpl extends AbstractCreateDatabaseStatementImpl {

    public function generate(DataSourceHandler $handler, DataSourceMetaData $datasource, array $options = NULL) {
        // a user needs to have 'CREATE SESSION' and 'CREATE USER' system privilege to execute the following statement
        $createUserSQL = "CREATE USER {$datasource->database} IDENTIFIED EXTERNALLY";

        $initialPrivilegeSQL = "GRANT CREATE SESSION TO {$datasource->database}";

        return array($createUserSQL, $initialPrivilegeSQL);
    }
}
