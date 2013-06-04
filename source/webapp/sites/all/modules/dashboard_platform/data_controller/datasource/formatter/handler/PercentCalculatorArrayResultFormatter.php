<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
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
