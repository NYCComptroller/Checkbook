<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




class ObjectArrayResultFormatter extends AbstractResultFormatter {

    public function formatRecord(array &$records = NULL, $record) {
        $result = parent::formatRecord($records, $record);
        if ($result) {
            $this->errorUnsupportedChainOfResultFormatters();
        }

        $object = new stdClass();
        foreach ($record as $columnName => $columnValue) {
            $index = strpos($columnName, '.');
            if ($index === FALSE) {
                $object->$columnName = $columnValue;
            }
            else {
                $properties = explode('.', $columnName);

                $obj = $object;
                for ($i = 0, $count = count($properties); $i < $count; $i++) {
                    $property = $properties[$i];
                    if ($i == ($count - 1)) {
                        $obj->$property = $columnValue;
                    }
                    else {
                        if (!isset($obj->$property)) {
                            $obj->$property = new stdClass();
                        }
                        $obj = $obj->$property;
                    }
                }
            }
        }

        $records[] = $object;

        return TRUE;
    }
}
