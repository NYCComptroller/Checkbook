<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class OracleApplyPaginationImpl extends AbstractApplyPaginationImpl {

    public function apply(DataSourceHandler $handler, &$sql, $startWith, $limit) {
        $firstRecordNumber = (isset($startWith) && ($startWith > 0)) ? ($startWith + 1) : NULL;

        $sql = 'SELECT n.*' . (isset($firstRecordNumber) ? ', rownum AS original_rownum' : '') . ' FROM ('
            . "\n" . StringHelper::indent($sql, Statement::$INDENT_SELECT_SECTION_ELEMENT, TRUE) . ') n';

        $lastRecordNumber = isset($limit) ? ((isset($firstRecordNumber) ? $firstRecordNumber : 1) + $limit - 1) : NULL;
        if (isset($lastRecordNumber)) {
            $sql .= "\n WHERE rownum <= " . $lastRecordNumber;
        }

        if (isset($firstRecordNumber)) {
            $sql = "SELECT * FROM (\n" . StringHelper::indent($sql, Statement::$INDENT_SELECT_SECTION_ELEMENT, TRUE) . ")\n WHERE original_rownum >= " . $firstRecordNumber;
        }
    }
}
