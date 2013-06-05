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


class StarSchemaDataSubmitter extends AbstractControllerDataSubmitter {

    protected function truncateStorage() {
        $dataStructureController = data_controller_ddl_get_instance();

        $dataStructureController->truncateCubeStorage($this->datasetName);
    }

    protected function submitRecordBatch(RecordMetaData $recordMetaData) {
        $dataManipulationController = data_controller_dml_get_instance();

        $identifierLoader = new StarSchemaLookupIdentifierLoader();
        $identifierLoader->load($this->datasetName, $recordMetaData, $this->recordsHolder);

        $factsDatasetName = StarSchemaNamingConvention::getFactsRelatedName($this->datasetName);

        if ($recordMetaData->findKeyColumns() == NULL) {
            $this->insertedRecordCount += $dataManipulationController->insertDatasetRecordBatch($factsDatasetName, $this->recordsHolder);
        }
        else {
            // even if we truncate the dataset we still need to support several references to the same record
            list($insertedRecordCount, $updatedRecordCount, $deletedRecordCount) =
                $dataManipulationController->insertOrUpdateOrDeleteDatasetRecordBatch($factsDatasetName, $this->recordsHolder);
            $this->insertedRecordCount += $insertedRecordCount;
            $this->updatedRecordCount += $updatedRecordCount;
            $this->deletedRecordCount += $deletedRecordCount;
        }
    }
}


class StarSchemaLookupIdentifierLoader extends AbstractLookupIdentifierLoader {

    public function selectLookupableColumns(RecordMetaData $recordMetaData) {
        $lookupableColumns = NULL;
        foreach ($recordMetaData->getColumns() as $column) {
            if ((isset($column->columnCategory)) && ($column->columnCategory === DatasetColumnCategories::ATTRIBUTE)) {
                $lookupableColumns[$column->columnIndex] = $column;
            }
        }

        return $lookupableColumns;
    }
}
