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




class WeightedGraderArrayResultFormatter extends ArrayResultFormatter {

    private $valuePropertyName = NULL;
    private $weightedGradeProperyName = NULL;

    public function __construct($valuePropertyName, $weightedGradeProperyName, ResultFormatter $parent = NULL) {
        parent::__construct($parent);

        $this->valuePropertyName = $valuePropertyName;
        $this->weightedGradeProperyName = $weightedGradeProperyName;
    }

    public function postFormatRecords(array &$records = NULL) {
        parent::postFormatRecords($records);

        if (!isset($records)) {
            return;
        }

        $valueMin = NULL;
        $valueMax = NULL;
        // preparing minimum and maximum values
        foreach ($records as $record) {
            if (!isset($record[$this->valuePropertyName])) {
                continue;
            }

            $value = $record[$this->valuePropertyName];

            $valueMin = MathHelper::min($valueMin, $value);
            $valueMax = MathHelper::max($value, $valueMax);
        }

        // generating weighted grade
        foreach ($records as &$record) {
            if (!isset($record[$this->valuePropertyName])) {
                continue;
            }

            $value = $record[$this->valuePropertyName];

            $weightedGrade = ($value - $valueMin) / ($valueMax - $valueMin);
            $record[$this->weightedGradeProperyName] = $weightedGrade;
        }
    }
}
