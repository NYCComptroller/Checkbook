<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class DefaultConcatenateValuesImpl extends AbstractConcatenateValuesImpl {

    public function concatenate(DataSourceHandler $handler, array $formattedValues) {
        $result = '';

        foreach ($formattedValues as $formattedValue) {
            if (strlen($result) > 0) {
                $result .= ' || ';
            }

            $result .= $formattedValue;
        }

        return $result;
    }
}
