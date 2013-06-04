<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




class UnionJoinController extends AbstractJoinController {

    public static $METHOD_NAME = 'Union';

    protected function joinSourceConfigurations(JoinController_SourceConfiguration $sourceConfigurationA, JoinController_SourceConfiguration $sourceConfigurationB) {
        // adding data from source A
        $result = isset($sourceConfigurationA->data) ? $sourceConfigurationA->adjustDataColumnNames() : NULL;

        // adding data from source B
        if (isset($sourceConfigurationB->data)) {
            $adjustedDataB = $sourceConfigurationB->adjustDataColumnNames();
            if (isset($result)) {
                $result = array_merge($result, $adjustedDataB);
            }
            else {
                $result = $adjustedDataB;
            }
        }

        return new JoinController_SourceConfiguration($result);
    }
}
