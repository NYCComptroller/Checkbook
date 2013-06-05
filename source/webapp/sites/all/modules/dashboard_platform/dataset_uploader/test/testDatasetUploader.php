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


define('MAX_COLUMN_VALUE_LENGTH', 255);

define('TABLE_PREFIX', 'ds');
define('TABLE_PREFIX_LENGTH', 'ds' + 13 + 1); // uniqid() + '_'

define('COLUMN_PREFIX', 'c_');

function testDatasetUploader_loadDatasetMetaData($filename, $delimiter, $skipRecordCount, $limitRecordCount, $isHeaderPresent) {
    echo "<h2>Analysing file structure</h2>\n";
    $metadataRequest = new DelimiterSeparatedFileUploadRequest();
    $metadataRequest->fullFileName = $filename;
    $metadataRequest->delimiter = $delimiter;
    $metadataRequest->isHeaderPresent = $isHeaderPresent;
    $metadataRequest->skipRecordCount = $skipRecordCount;
    $metadataRequest->limitRecordCount = $limitRecordCount;
    var_dump($metadataRequest);

    $simpleDataSkipRecordCount = 0;
    $sampleDataLimitRecordCount = 5;
    $sampleDataProvider = new SampleDataPreparer($simpleDataSkipRecordCount, $sampleDataLimitRecordCount);

    $recordCount = data_uploader_get_source_metadata(
        $metadataRequest,
        array(
            new ColumnValueTrimmer(MAX_COLUMN_VALUE_LENGTH),
            new ColumnMetaDataPreparer(DrupalDataSourceHandler::$DATASOURCE__DEFAULT, TABLE_PREFIX_LENGTH, COLUMN_PREFIX),
            new ColumnTypeAutoDetector($simpleDataSkipRecordCount, $sampleDataLimitRecordCount),
            new ColumnCategoryPreparer(ColumnCategoryPreparer::$EVENT_NAME__AFTER_PROCESSING),
            $sampleDataProvider));
    $dataset = $metadataRequest->metadata;

    echo "<h2>Processed Records</h2>\n";
    echo "$recordCount\n";

    echo "<h2>Meta Data</h2>\n";
    var_dump($dataset);

    echo "<h2>Sample Data</h2>\n";
    var_dump($sampleDataProvider->records);

    return $dataset;
}

function testDatasetUploader_addDummyColumn($dataset) {
    $column = $dataset->columns[0];

    $dummyColumn = $dataset->registerColumn($column->name . '_2');
    $dummyColumn->description = $column->description;
    $dummyColumn->type = clone $column->type;
    $dummyColumn->columnIndex = $column->columnIndex;
    $dummyColumn->columnCategory = $column->columnCategory;

    $column->used = FALSE;
}

function testDatasetUploader_excludeColumns($dataset, $excludedColumnNames) {
    foreach ($excludedColumnNames as $excludedColumnName) {
        $dataset->getColumn($excludedColumnName)->used = FALSE;
    }
}

function testDatasetUploader_registerDatasetStorage($dataset) {
    $dataController = data_controller_get_instance();

    echo "<h2>Registering dataset</h2>\n";

    $nameSuffix = uniqid();
    $dataset->name = NameSpaceHelper::addNameSpace(DrupalDataSourceHandler::$NAME_SPACE__DEFAULT, $nameSuffix);
    $dataset->source = TABLE_PREFIX . $nameSuffix;
    $dataset->datasourceName = DrupalDataSourceHandler::$DATASOURCE__DEFAULT;

    $dataController->registerDatasetStorage(DrupalDataSourceHandler::$NAME_SPACE__DEFAULT, $dataset);

    $cube = StarSchemaCubeMetaData::initializeFromDataset(DrupalDataSourceHandler::$DATASOURCE__DEFAULT, $dataset);

    $dataController->registerCubeStorage(DrupalDataSourceHandler::$NAME_SPACE__DEFAULT, $cube);

    echo "$dataset->name\n";
}

function testDatasetUploader_createDatasetStorage($dataset) {
    $dataController = data_controller_get_instance();

    echo "<h2>Creaing dataset storage</h2>\n";
    $dataController->createDatasetStorage($dataset->name);
    $dataController->createCubeStorage($dataset->name);
    echo "Completed\n";
}

function testDatasetUploader_uploadFile($filename, $delimiter, $skipRecordCount, $limitRecordCount, $isHeaderPresent, $dataset, $truncateBeforeProceed) {
    echo "<h2>Uploading file</h2>\n";
    $uploadRequest = new DelimiterSeparatedFileUploadRequest();
    $uploadRequest->fullFileName = $filename;
    $uploadRequest->delimiter = $delimiter;
    $uploadRequest->isHeaderPresent = $isHeaderPresent;
    $uploadRequest->skipRecordCount = $skipRecordCount;
    $uploadRequest->limitRecordCount = $limitRecordCount;
    $uploadRequest->metadata = $dataset;
    var_dump($uploadRequest);

    $dataSubmitter = new StarSchemaDataSubmitter($dataset->name, $truncateBeforeProceed);

    $timeStart = microtime(TRUE);
    $recordCount = data_uploader_store_source_data(
        $uploadRequest,
        array(
            new ColumnValueTrimmer(MAX_COLUMN_VALUE_LENGTH),
            new ColumnValueTypeAdjuster(),
            new FlatSchemaDataSubmitter($dataset->name, $truncateBeforeProceed),
            $dataSubmitter));
    $time = 1000 * (microtime(TRUE) - $timeStart);

    echo "<h2>File uploaded</h2>\n";
    echo "Added records: $dataSubmitter->insertedRecordCount\n";
    echo "Updated records: $dataSubmitter->updatedRecordCount\n";
    echo "Execution time for $recordCount imported record(s) is $time ms (" . ($time / (($recordCount == 0) ? 1 : $recordCount)) . " ms/record)\n";
}

function testDatasetUploader($filename, $delimiter, $skipRecordCount, $limitRecordCount, $isHeaderPresent, $excludedColumnNames, $testDummyColumn, $testTruncate) {
    // loading file meta data
    $dataset = testDatasetUploader_loadDatasetMetaData($filename, $delimiter, $skipRecordCount, 100/*$limitRecordCount*/, $isHeaderPresent);

    // adding 'dummy' column
    if ($testDummyColumn) {
        testDatasetUploader_addDummyColumn($dataset);
    }

    // excluding columns
    if (isset($excludedColumnNames)) {
        testDatasetUploader_excludeColumns($dataset, $excludedColumnNames);
    }

    // registering dataset
    testDatasetUploader_registerDatasetStorage($dataset);

    // creating dataset storage
    testDatasetUploader_createDatasetStorage($dataset);

    // uploading file data
    testDatasetUploader_uploadFile($filename, $delimiter, $skipRecordCount, ($testTruncate ? 1 : $limitRecordCount), $isHeaderPresent, $dataset, FALSE);
    if ($testTruncate) {
        testDatasetUploader_uploadFile($filename, $delimiter, $skipRecordCount, $limitRecordCount, $isHeaderPresent, $dataset, TRUE);
    }
}
