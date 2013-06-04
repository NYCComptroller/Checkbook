<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class DefaultPrepareUpdateStatementImpl extends AbstractPrepareUpdateStatementImpl {

    protected function prepareColumnExpressions(array $columnValues, $delimiter) {
        $s = NULL;

        foreach ($columnValues as $columnName => $value) {
            if (isset($s)) {
                $s .= $delimiter;
            }

            $s .=  $columnName . ' = ' . $value;
        }

        return $s;
    }

    public function prepare(DataSourceHandler $handler, $tableName, array $setColumnValues = NULL, array $whereColumnValues = NULL) {
        // we do not need to update any columns. Just ignoring this request
        if (!isset($setColumnValues)) {
            return NULL;
        }

        $sql = "UPDATE $tableName SET " . $this->prepareColumnExpressions($setColumnValues, ', ');
        if (isset($whereColumnValues)) {
            $sql .= ' WHERE ' . $this->prepareColumnExpressions($whereColumnValues, ' AND ');
        }

        return $sql;
    }
}
