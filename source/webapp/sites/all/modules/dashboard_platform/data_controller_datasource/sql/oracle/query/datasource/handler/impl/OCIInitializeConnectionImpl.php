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


class OCIInitializeConnectionImpl extends AbstractInitializeConnectionImpl {

    public function initialize(DataSourceHandler $handler, DataSourceMetaData $datasource) {
        if (!isset($datasource->database)) {
            throw new IllegalStateException(t('Entry name from tnsnames.ora is not provided'));
        }

        $connection = OCIImplHelper::oci_connect($datasource->username, $datasource->password, $datasource->database);

        $sql = array(
            'ALTER SESSION SET NLS_SORT=ASCII7_AI',
            'ALTER SESSION SET NLS_COMP=LINGUISTIC');

        $statementExecutor = new OCIExecuteStatementImpl();
        $statementExecutor->execute($handler, $connection, $sql);

        return $connection;
    }
}
