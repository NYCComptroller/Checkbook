<?php
/**
* This file is part of the Checkbook NYC financial transparency software.
* 
* Copyright (C) 2012, 2013 New York City
* 
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU Affero General Public License as
* published by the Free Software Foundation, either version 3 of the
* License, or (at your option) any later version.
* 
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU Affero General Public License for more details.
* 
* You should have received a copy of the GNU Affero General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
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

        $result = [];
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
