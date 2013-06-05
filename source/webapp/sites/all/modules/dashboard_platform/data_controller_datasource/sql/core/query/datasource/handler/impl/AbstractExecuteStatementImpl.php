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


abstract class AbstractExecuteStatementImpl extends AbstractObject {

    public function execute(DataSourceHandler $handler, $connection, $sql) {
        $affectedRecordCount = 0;

        if (isset($sql)) {
            if (is_array($sql)) {
                foreach ($sql as $individualStatement) {
                    $affectedRecordCount += $this->executeIndividualStatement($handler, $connection, $individualStatement);
                }
            }
            else {
                $affectedRecordCount += $this->executeIndividualStatement($handler, $connection, $sql);
            }
        }

        return $affectedRecordCount;
    }

    abstract protected function executeIndividualStatement(DataSourceHandler $handler, $connection, $sql);
}
