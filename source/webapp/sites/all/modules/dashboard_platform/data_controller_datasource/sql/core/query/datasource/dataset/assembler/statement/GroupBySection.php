<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class GroupByColumnSection extends AbstractColumnSection {

    private $column = NULL;

    public function __construct(ColumnSection $column) {
        parent::__construct();
        $this->column = $column;
    }

    public function assemble($tableAlias) {
        return $this->column->assembleColumnName($tableAlias);
    }
}
