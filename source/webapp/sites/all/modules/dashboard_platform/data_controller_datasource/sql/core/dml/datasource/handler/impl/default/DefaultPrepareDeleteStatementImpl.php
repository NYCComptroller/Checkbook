<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class DefaultPrepareDeleteStatementImpl extends AbstractPrepareDeleteStatementImpl {

    public function prepare(DataSourceHandler $handler, $tableName, array $keys = NULL) {
        $sqls = NULL;

        if (isset($keys)) {
            $header = "DELETE FROM $tableName";

            foreach ($keys as $key) {
                $s = NULL;

                foreach ($key as $columnName => $value) {
                    if (isset($s)) {
                        $s .= ' AND ';
                    }
                    else {
                        $s = ' WHERE ';
                    }

                    $s .=  $columnName;

                    if (is_array($value)) {
                        if (count($value) == 1) {
                            $s .= ' = ' . $value[0];
                        }
                        else {
                            $s .= ' IN (' . implode(', ', $value) . ')';
                        }
                    }
                    else {
                        $s .= ' = ' . $value;
                    }
                }

                $sqls[] = $header . $s;
            }
        }

        return $sqls;
    }
}
