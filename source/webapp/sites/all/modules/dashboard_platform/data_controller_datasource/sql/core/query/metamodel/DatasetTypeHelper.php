<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class DatasetTypeHelper {

    const DATASET_SOURCE_TYPE__TABLE = 'Table';
    const DATASET_SOURCE_TYPE__SUBQUERY = 'SubQuery';
    const DATASET_SOURCE_TYPE__DYNAMIC = 'Dynamic';

    /**
     * Retrieves dataset metadata object and checks if the dataset source is a database table
     *
     * @param $datasetName
     * @return DatasetMetaData
     */
    public static function getTableDataset($datasetName) {
        $metamodel = data_controller_get_metamodel();

        $dataset = $metamodel->getDataset($datasetName);

        self::checkDatasetTableName($dataset);

        return $dataset;
    }

    /*
     * Checks that dataset source is a database table
     */
    protected static function checkDatasetTableName(DatasetMetaData $dataset) {
        $datasetSourceType = self::detectDatasetSourceType($dataset);
        if ($datasetSourceType != self::DATASET_SOURCE_TYPE__TABLE) {
            throw new IllegalArgumentException(t(
                "Only a table can be used as a storage for '@datasetName' dataset: @datasetSourceType",
                array('@datasetName' => $dataset->publicName, '@datasetSourceType' => $datasetSourceType)));
        }
    }

    /*
     * Detects type of dataset source.
     * The following are supported types:
     *   - database table
     *   - custom SQL statement: it will be used as a subquery
     *   - custom configuration for an assembler: SQL is prepared by the assembler
     */
    public static function detectDatasetSourceType(DatasetMetaData $dataset) {
        if (isset($dataset->assembler)) {
            return self::DATASET_SOURCE_TYPE__DYNAMIC;
        }
        elseif (isset($dataset->source)) {
            $source = trim($dataset->source);
            $isTableName = strpos($source, ' ') === FALSE;

            return $isTableName ? self::DATASET_SOURCE_TYPE__TABLE : self::DATASET_SOURCE_TYPE__SUBQUERY;
        }

        LogHelper::log_error($dataset);
        throw new IllegalArgumentException(t(
            'Could not detect type of dataset source for the dataset: @datasetName',
            array('@datasetName' => $dataset->publicName)));
    }
}
