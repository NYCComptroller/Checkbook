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




class AggregatedRowFlattenerResultFormatter extends RowFlattenerResultFormatter {

    public static $PROPERTY_NAME_PREFIX__SUBJECT_AGGREGATED = 'aggregated';

    public function postFormatRecords(array &$records = NULL) {
        parent::postFormatRecords($records);

        if (!isset($records)) {
            return;
        }

        foreach ($records as &$record) {
            foreach ($this->adjustedSubjectPropertyNames as $subjectPropertyName) {
                $aggregation = 0.0;
                foreach ($record as $propertyName => $propertyValue) {
                    if (strpos($propertyName, $subjectPropertyName) === 0) {
                        $aggregation += $propertyValue;
                    }
                }

                $record[self::$PROPERTY_NAME_PREFIX__SUBJECT_AGGREGATED . '_' . $subjectPropertyName] = $aggregation;
            }
        }
    }
}
