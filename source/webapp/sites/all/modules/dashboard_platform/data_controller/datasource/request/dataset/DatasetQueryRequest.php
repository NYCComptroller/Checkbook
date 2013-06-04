<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




class DatasetQueryRequest extends AbstractDatasetQueryRequest {

    public $columns = NULL;

    public function addColumn($column) {
        ReferencePathHelper::checkReference($column);

        ArrayHelper::addUniqueValue($this->columns, $column);
    }

    public function addColumns($columns) {
        if (!isset($columns)) {
            return;
        }

        if (is_array($columns)) {
            foreach ($columns as $column) {
                $this->addColumn($column);
            }
        }
        else {
            $this->addColumn($columns);
        }
    }
}
