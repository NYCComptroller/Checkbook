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


class ReferencedDatasetComparator extends AbstractComparator {

    protected function isDependent(DatasetMetaData $dataset, DatasetMetaData $datasetB) {
        $referenceCount = 0;

        foreach ($dataset->getColumns() as $column) {
            list($referencedDatasetName) = ReferencePathHelper::splitReference($column->type->applicationType);
            if (isset($referencedDatasetName)) {
                $referenceCount++;
            }
            else {
                continue;
            }

            if ($referencedDatasetName == $datasetB->name) {
                return array(TRUE, NULL);
            }
        }

        return array(FALSE, $referenceCount);
    }

    public function compare($datasetA, $datasetB) {
        // checking if dataset B is referenced by dataset A
        list($referencedB, $referenceCountA) = $this->isDependent($datasetA, $datasetB);

        // checking if dataset A is referenced by dataset B
        list($referencedA, $referenceCountB) = $this->isDependent($datasetB, $datasetA);

        if ($referencedA) {
            if ($referencedB) {
                throw new UnsupportedOperationException(t(
                    "Datasets '@datasetNameA' and '@datasetNameB' are cross referenced",
                    array('@datasetNameA' => $datasetA->publicName, '@datasetNameB' => $datasetB->publicName)));
            }
            else {
                return -1;
            }
        }
        elseif ($referencedB) {
            return 1;
        }

        // at least one dataset does not depend on any other datasets
        if (($referenceCountA == 0) || ($referenceCountB == 0)) {
            return $referenceCountA - $referenceCountB;
        }

        if (isset($datasetA->nid) && isset($datasetB->nid)) {
            return $datasetA->nid - $datasetB->nid;
        }

        throw new UnsupportedOperationException(t(
            "Cannot compare '@datasetNameA' and '@datasetNameB' datasets",
            array('@datasetNameA' => $datasetA->publicName, '@datasetNameB' => $datasetB->publicName)));
    }
}
