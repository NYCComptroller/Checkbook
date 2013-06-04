<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
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
