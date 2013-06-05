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


class OracleCreateDatabaseStatementImpl extends AbstractCreateDatabaseStatementImpl {

    public function generate(DataSourceHandler $handler, DataSourceMetaData $datasource, array $options = NULL) {
        // a user needs to have 'CREATE SESSION' and 'CREATE USER' system privilege to execute the following statement
        $createUserSQL = "CREATE USER {$datasource->database} IDENTIFIED EXTERNALLY";

        $initialPrivilegeSQL = "GRANT CREATE SESSION TO {$datasource->database}";

        return array($createUserSQL, $initialPrivilegeSQL);
    }
}
