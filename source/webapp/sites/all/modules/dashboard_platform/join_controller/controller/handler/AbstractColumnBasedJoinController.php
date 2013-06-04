<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




abstract class AbstractColumnBasedJoinController extends AbstractJoinController {

    abstract protected function preselectSourceConfiguration(JoinController_SourceConfiguration $sourceConfigurationA, JoinController_SourceConfiguration $sourceConfigurationB);

    abstract protected function joinHash(array &$result, array &$hashedSourceA, array &$hashedSourceB);

    protected function joinSourceConfigurations(JoinController_SourceConfiguration $sourceConfigurationA, JoinController_SourceConfiguration $sourceConfigurationB) {
        $preselectedSourceConfiguration = $this->preselectSourceConfiguration($sourceConfigurationA, $sourceConfigurationB);
        if ($preselectedSourceConfiguration !== FALSE) {
            return JoinController_SourceConfiguration::getAdjustedSourceConfiguration($preselectedSourceConfiguration);
        }

        $hashedSourceA = $this->hashSource($sourceConfigurationA);
        $hashedSourceB = $this->hashSource($sourceConfigurationB);

        $result = array();
        $this->joinHash($result, $hashedSourceA, $hashedSourceB);

        return new JoinController_SourceConfiguration($result, $sourceConfigurationA->keyColumnNames);
    }

    protected function hashSource(JoinController_SourceConfiguration $sourceConfiguration) {
        $sourceConfiguration->checkRequiredKeyColumnNames();

        $hashedSource = NULL;

        if (isset($sourceConfiguration->data)) {
            foreach ($sourceConfiguration->data as $record) {
                $key = '';
                foreach ($sourceConfiguration->keyColumnNames as $keyColumnName) {
                    $key .= $record[$keyColumnName] . ':';
                }

                $hashedSource[$key][] = $sourceConfiguration->adjustRecordColumnNames($record);
            }
        }

        return $hashedSource;
    }

    protected function mergeRecords(array $recordA, array $recordB) {
        $record = $recordA;

        foreach ($recordB as $columnName => $newValue) {
            if (isset($record[$columnName])) {
                $oldValue = $record[$columnName];
                if ($oldValue == $newValue) {
                    continue;
                }
                else {
                    throw new IllegalArgumentException(t(
                    	"Values for '@columnName' column cannot be merged: @oldValue, @newValue",
                        array('@columnName' => $columnName, '@oldValue' => $oldValue, '@newValue' => $newValue)));
                }
            }

            $record[$columnName] = $newValue;
        }

        return $record;
    }
}
