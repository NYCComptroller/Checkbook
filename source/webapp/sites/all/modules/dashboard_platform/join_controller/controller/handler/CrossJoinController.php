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
