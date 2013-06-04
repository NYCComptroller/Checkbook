<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




class InnerJoinController extends AbstractColumnBasedJoinController {

    public static $METHOD_NAME = 'Inner';

    protected function preselectSourceConfiguration(JoinController_SourceConfiguration $sourceConfigurationA, JoinController_SourceConfiguration $sourceConfigurationB) {
        return isset($sourceConfigurationA->data)
            ? (isset($sourceConfigurationB->data)
                ? FALSE // we need to join the sources
                : $sourceConfigurationB)
            : $sourceConfigurationA;
    }

    protected function joinHash(array &$result, array &$hashedSourceA, array &$hashedSourceB) {
        foreach ($hashedSourceA as $keyA => $recordsA) {
            // skipping the record which does not have a corresponding record in other data set
            if (!isset($hashedSourceB[$keyA])) {
                continue;
            }

            $recordsB = $hashedSourceB[$keyA];

            foreach ($recordsA as $recordA) {
                foreach ($recordsB as $recordB) {
                    $result[] = $this->mergeRecords($recordA, $recordB);
                }
            }
        }
    }
}
