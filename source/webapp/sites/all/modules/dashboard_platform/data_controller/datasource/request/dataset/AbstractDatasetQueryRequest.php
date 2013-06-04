<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




abstract class AbstractDatasetQueryRequest extends AbstractQueryRequest {

    public function getDatasetName() {
        return $this->sourceName;
    }

    public function addQueryValue($index, $name, $value) {
        ReferencePathHelper::checkReference($name);

        $this->queries[$index][$name][] = $value;
    }

    public function addQueryValues($index, $name, $value) {
        foreach ($value as $v) {
            $this->addQueryValue($index, $name, $v);
        }
    }

    public function addCompositeQueryValues($compositeQuery) {
        if (!isset($compositeQuery)) {
            return;
        }

        $isIndexedArray = ArrayHelper::isIndexedArray($compositeQuery);
        if ($isIndexedArray && (count($compositeQuery) === 1)) {
            $compositeQuery = $compositeQuery[0];
            $isIndexedArray = FALSE;
        }

        if ($isIndexedArray) {
            foreach ($compositeQuery as $query) {
                $this->addCompositeQueryValues($query);
            }
        }
        else {
            $index = count($this->queries);

            foreach ($compositeQuery as $name => $value) {
                $this->addQueryValues($index, $name, $value);
            }
        }
    }
}
