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




class PercentCalculatorArrayResultFormatter extends ArrayResultFormatter {

    private $amountPropertyNames = NULL;
    private $generatedColumnPrefix = NULL;
    private $generatedColumnSuffix = NULL;

    public function __construct($amountPropertyNames, $generatedColumnPrefix = NULL, $generatedColumnSuffix = '_percent', ResultFormatter $parent = NULL) {
        parent::__construct($parent);

        $this->amountPropertyNames = is_array($amountPropertyNames) ? $amountPropertyNames : array($amountPropertyNames);
        $this->generatedColumnPrefix = $generatedColumnPrefix;
        $this->generatedColumnSuffix = $generatedColumnSuffix;
    }

    public function __clone() {
        parent::__clone();

        $this->amountPropertyNames = ArrayHelper::cloneArray($this->amountPropertyNames);
    }

    public function postFormatRecords(array &$records = NULL) {
        parent::postFormatRecords($records);

        if (!isset($records)) {
            return;
        }

        // calculating total amounts
        $totals = NULL;
        foreach ($this->amountPropertyNames as $amountPropertyName) {
            $totals[$amountPropertyName] = 0.0;
        }
        foreach ($records as $record) {
            foreach ($this->amountPropertyNames as $amountPropertyName) {
                $amount = $record[$amountPropertyName];
                if (isset($amount)) {
                    $totals[$amountPropertyName] += $amount;
                }
            }
        }

        // calculating percents
        foreach ($records as &$record) {
            foreach ($this->amountPropertyNames as $amountPropertyName) {
                $amount = $record[$amountPropertyName];
                if (isset($amount)) {
                    $total = $totals[$amountPropertyName];
                    $percent = ($total == 0.0) ? 'N/A' : ($amount / $total);

                    $percentPropertyName = '';
                    if (isset($this->generatedColumnPrefix)) {
                        $percentPropertyName = $this->generatedColumnPrefix . $percentPropertyName;
                    }
                    $percentPropertyName .= $amountPropertyName;
                    if (isset($this->generatedColumnSuffix)) {
                        $percentPropertyName .= $this->generatedColumnSuffix;
                    }

                    $record[$percentPropertyName] = $percent;
                }
            }
        }
        unset($record);
    }
}
