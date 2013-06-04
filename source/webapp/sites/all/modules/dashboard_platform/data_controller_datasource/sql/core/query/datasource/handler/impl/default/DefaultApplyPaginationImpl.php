<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class DefaultApplyPaginationImpl extends AbstractApplyPaginationImpl {

    public function apply(DataSourceHandler $handler, &$sql, $startWith, $limit) {
        $sql .= "\n LIMIT " . (isset($limit) ? $limit : PHP_INT_MAX);
        if (isset($startWith) && ($startWith > 0)) {
            $sql .= ' OFFSET ' . $startWith;
        }
    }
}
