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

            $index = $this->queries ? count($this->queries) : 0;

            foreach ($compositeQuery as $name => $value) {
                $this->addQueryValues($index, $name, $value);
            }
        }
    }
}
