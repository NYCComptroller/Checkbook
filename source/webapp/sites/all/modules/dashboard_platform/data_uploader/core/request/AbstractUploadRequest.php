<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


abstract class AbstractUploadRequest extends AbstractMetaDataRequest {

    // are column names present in header of a stream of data
    public $isHeaderPresent = TRUE;

    // 'parser' will skip the following number of records before start processing
    public $skipRecordCount = 0;
    // 'parser' will process only the following number of records. <NULL> means ALL
    public $limitRecordCount = NULL;

    abstract protected function initiateDataParser();

    public function prepareDataParser() {
        $parser = $this->initiateDataParser();
        $parser->metadata = $this->metadata;
        $parser->isHeaderPresent = $this->isHeaderPresent;
        $parser->skipRecordCount = $this->skipRecordCount;
        $parser->limitRecordCount = $this->limitRecordCount;

        return $parser;
    }

    abstract protected function initiateDataProvider();

    public function prepareDataProvider() {
        $dataProvider = $this->initiateDataProvider();

        return $dataProvider;
    }
}
