<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
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
