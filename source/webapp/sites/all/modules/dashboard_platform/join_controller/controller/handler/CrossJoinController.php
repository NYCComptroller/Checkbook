<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




class CrossJoinController extends AbstractJoinController {

    public static $METHOD_NAME = 'Cross';

    protected function joinSourceConfigurations(JoinController_SourceConfiguration $sourceConfigurationA, JoinController_SourceConfiguration $sourceConfigurationB) {
        // preparing data from source A
        $adjustedDataA = isset($sourceConfigurationA->data) ? $sourceConfigurationA->adjustDataColumnNames() : NULL;

        // preparing data from source B
        if (isset($sourceConfigurationB->data)) {
            $adjustedDataB = $sourceConfigurationB->adjustDataColumnNames();
            if (isset($adjustedDataA)) {
                $result = NULL;
                // crossing records
                foreach ($adjustedDataA as $recordA) {
                    foreach ($adjustedDataB as $recordB) {
                        $result[] = array_merge($recordA, $recordB);
                    }
                }
            }
            else {
                $result = $adjustedDataB;
            }
        }
        else {
            $result = $adjustedDataA;
        }

        return new JoinController_SourceConfiguration($result);
    }
}
