<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class DelimiterDataParser extends AbstractDataParser {

    private static $MAX_ATTEMPTS_TO_RESOLVE_PARSING_ISSUES = 1;
    private static $PARSING_METHOD__SUPPORT_BACKSLASH = 1;

    private $stringDataTypeHandler = NULL;
    private $delimiter = NULL;

    public function __construct($delimiter) {
        parent::__construct();

        $this->stringDataTypeHandler = new StringDataTypeHandler();

        // preparing column value delimiter
        if (!isset($delimiter) || (strlen($delimiter) === 0)) {
            throw new IllegalStateException(t('Delimiter has not been defined'));
        }
        $this->delimiter = $delimiter;
    }

    protected function parseNextRecord(AbstractDataProvider $dataProvider, $attempt = 0) {
        $line = $dataProvider->readLine();
        if ($line === FALSE) {
            return NULL;
        }

        $record = NULL;

        $delimiterSize = strlen($this->delimiter);

        // the following flag is used to support lines where first column value is empty string
        $isDelimiterExpected = FALSE;

        // preparing line properties
        $index = 0;
        $lineLength = strlen($line);

        // parsing the line
        while ($index < $lineLength) {
            // to resolve the following issues: <,  "...."  ,>
            if ($line{$index} == ' ') {
                $index++;
                continue;
            }

            if ($isDelimiterExpected) {
                if (substr($line, $index, $delimiterSize) == $this->delimiter) {
                    $index += $delimiterSize;
                }
                else {
                    // reached end of line
                }
            }

            if (($index < $lineLength) && ($line{$index} == '"')) {
                $originalLineNumber = NULL;
                $originalLinePosition = $index + 1;

                $value = '';

                $index++;
                while (TRUE) {
                    do {
                        $i = strpos($line, '"', $index);
                        if ($i === FALSE) {
                            if (!isset($originalLineNumber)) {
                                $originalLineNumber = $dataProvider->getCurrentLineNumber();
                            }

                            // it is possible that the record spreads across several lines
                            $s = $dataProvider->readLine();
                            if ($s === FALSE) {
                                LogHelper::log_error($record);
                                $e = new DataParserException(t(
                                    "Inconsistent file structure. Could not find second \" in row column value (line: @lineNumber; position: @linePosition)",
                                    array('@lineNumber' => $originalLineNumber, '@linePosition' => $originalLinePosition)));
                                throw $e;
                            }
                            $line .= ' ' . $s;
                            $lineLength += strlen($s) + 1; // + 1 is for space
                        }
                    }
                    while ($i === FALSE);

                    if (($attempt == self::$PARSING_METHOD__SUPPORT_BACKSLASH) && ($line{$i - 1} == '\\')) {
                        // support for nested " which is marked as '\"'
                        $value .= substr($line, $index, $i - $index - 1) . '"';
                        $index = $i + 1;
                    }
                    elseif ((($i + 1) < $lineLength) && ($line{$i + 1} == '"')) {
                        // support for nested " which is marked as '""'
                        $value .= substr($line, $index, $i - $index + 1);
                        $index = $i + 2;
                    }
                    else {
                        $value .= substr($line, $index, $i - $index);
                        $index = $i + 1;
                        break;
                    }
                }
            }
            else {
                $indexEnd = strpos($line, $this->delimiter, $index);
                if ($indexEnd === FALSE) {
                    $indexEnd = $lineLength;
                }

                $value = ($index < $indexEnd) ? substr($line, $index, $indexEnd - $index) : '';

                $index = $indexEnd;
            }

            // post-converting value
            $value = trim($value);

            $record[] = $value;

            $isDelimiterExpected = TRUE;
        }

        // when empty line is passed to this method it means we have one column with empty value
        if (!isset($record)) {
            $record[] = '';
        }

        return $record;
    }

    protected function loadMetaData(AbstractDataProvider $dataProvider, array $dataSubmitters = NULL) {
        $dataProvider->startReading();
        $columnNames = $this->parseNextRecord($dataProvider);
        if ($this->isHeaderPresent) {
            $dataProvider->endReading();

            if (!isset($columnNames)) {
                throw new IllegalStateException(t('Upload operation resulted in error. No data was detected'));
            }
        }
        else {
            // we intentionally processed the row to just identify number of columns
            $dataProvider->rollbackReading();

            // it means the file contains one empty column
            if (!isset($columnNames)) {
                $columnNames[] = '';
            }
        }

        $providerMetaData = $this->initiateMetaData();
        for ($i = 0, $count = count($columnNames); $i < $count; $i++) {
            $originalColumnName = $this->isHeaderPresent
                ? $this->stringDataTypeHandler->castValue($columnNames[$i])
                : NULL;

            $column = $providerMetaData->initiateColumn();
            $column->sourceName = $originalColumnName;
            $column->columnIndex = $i;
            $column->description = $originalColumnName;

            $this->prepareMetaDataColumn($dataSubmitters, $providerMetaData, $column, $originalColumnName);

            // adjusting public name if not provided
            if (!isset($column->publicName)) {
                $column->publicName = $column->name;
            }

            $providerMetaData->registerColumnInstance($column);
        }

        // comparing structure with existing meta data
        if (isset($this->metadata)) {
            // number of columns has to be the same
            $existingColumnCount = $this->metadata->getColumnCount(FALSE);
            $newColumnCount = $providerMetaData->getColumnCount(FALSE);
            if ($existingColumnCount !== $newColumnCount) {
                LogHelper::log_error($this->metadata);
                LogHelper::log_error($providerMetaData);
                throw new DataParserException(t(
                    'Loaded structure with @newColumnCount column(s) vs @existingColumnCount in original dataset',
                    array('@existingColumnCount' => $existingColumnCount, '@newColumnCount' => $newColumnCount)));
            }

            // if header is present comparing column names
            if ($this->isHeaderPresent) {
                foreach ($providerMetaData->columns as $column) {
                    $structureMatches = TRUE;

                    $oldColumn = $this->metadata->findColumnByIndex($column->columnIndex);
                    if (isset($oldColumn)) {
                        $structureMatches = StringHelper::compareValues($oldColumn->sourceName, $column->sourceName);
                    }
                    else {
                        // for some reason a column with the column index does not exist in old meta data
                        $structureMatches = FALSE;
                    }

                    if (!$structureMatches) {
                        LogHelper::log_error($column);
                        LogHelper::log_error($oldColumn);
                        throw new DataParserException(t(
                            'Structure of dataset provided for update operation is different from original dataset (column @columnNumber: @columnName)',
                            array('@columnNumber' => ($column->columnIndex + 1), '@columnName' => $column->publicName)));
                    }
                }
            }
        }
        else {
            $this->metadata = $providerMetaData;
        }
    }

    protected function postProcessColumnValues(array &$record) {
        foreach ($this->metadata->columns as $column) {
            if ($column->isUsed()) {
                $record[$column->columnIndex] = isset($record[$column->columnIndex])
                    ? $this->stringDataTypeHandler->castValue($record[$column->columnIndex])
                    : NULL;
            }
            else {
                unset($record[$column->columnIndex]);
            }
        }
    }

    public function parse(AbstractDataProvider $dataProvider, array $dataSubmitters = NULL) {
        $skippedRecordCount = 0;
        $loadedRecordCount = 0;

        if ($dataProvider->openResource()) {
            LogHelper::log_notice(t(
                'Parsing @limitRecordCount records. Skipping first @skipRecordCount records (memory usage: @memoryUsed) ...',
                array(
                    '@skipRecordCount' => $this->skipRecordCount,
                    '@limitRecordCount' => (isset($this->limitRecordCount) ? $this->limitRecordCount : t('all')),
                    '@memoryUsed' => memory_get_usage())));

            try {
                if ($this->startProcessing($dataSubmitters)) {
                    // preparing list of columns
                    $this->prepareMetaData($dataProvider, $dataSubmitters);

                    $metadataColumnCount = $this->metadata->getColumnCount(FALSE);

                    if ((!isset($this->limitRecordCount) || ($this->limitRecordCount > 0))
                            && $this->beforeProcessingRecords($dataSubmitters, $dataProvider)) {
                        // processing records
                        $fileProcessedCompletely = FALSE;
                        while (!isset($this->limitRecordCount) || ($loadedRecordCount < $this->limitRecordCount)) {
                            $dataProvider->startReading();
                            $record = $this->parseNextRecord($dataProvider);

                            // number of loaded columns should match number of columns in meta data
                            if (isset($record)) {
                                $attempt = 1;
                                while (TRUE) {
                                    $recordColumnCount = count($record);
                                    if ($recordColumnCount == $metadataColumnCount) {
                                        break;
                                    }
                                    else {
                                        if ($attempt > self::$MAX_ATTEMPTS_TO_RESOLVE_PARSING_ISSUES) {
                                            $dataProvider->endReading();
                                            LogHelper::log_error($this->metadata);
                                            LogHelper::log_error($record);
                                            throw new DataParserException(t(
                                                "Expected to load values for @metadataColumnCount columns. Loaded @loadedColumnCount (line: @lineNumber)",
                                                array('@metadataColumnCount' => $metadataColumnCount, '@loadedColumnCount' => $recordColumnCount, '@lineNumber' => $dataProvider->getCurrentLineNumber())));
                                        }

                                        $dataProvider->rollbackReading();
                                        $dataProvider->startReading();
                                        $record = $this->parseNextRecord($dataProvider, $attempt);

                                        $attempt++;
                                    }
                                }
                            }
                            $dataProvider->endReading();

                            // checking if we reached the end
                            if (!isset($record)) {
                                $fileProcessedCompletely  = TRUE;
                                break;
                            }

                            // skipping required number of records
                            if ($skippedRecordCount < $this->skipRecordCount) {
                                $skippedRecordCount++;
                                continue;
                            }

                            $this->postProcessColumnValues($record);

                            // checking if we need to skip processing the record
                            $recordNumber = $dataProvider->getCurrentLineNumber();
                            if ($this->beforeProcessingRecord($dataSubmitters, $recordNumber, $record)) {
                                $this->processRecord($dataSubmitters, $recordNumber, $record);
                                $this->afterProcessingRecord($dataSubmitters, $recordNumber, $record);

                                $loadedRecordCount++;
                                if (($loadedRecordCount % 1000) == 0) {
                                    LogHelper::log_info(t(
                                        'Processed @recordCount records so far (memory usage: @memoryUsed)',
                                        array('@recordCount' => $loadedRecordCount, '@memoryUsed' => memory_get_usage())));
                                }
                            }
                        }

                        $this->afterProcessingRecords($dataSubmitters, $fileProcessedCompletely);
                    }

                    $this->finishProcessing($dataSubmitters);
                }
            }
            catch (DataParserException $e) {
                LogHelper::log_error($e->getFile() . ':' . $e->getLine());

                try {
                    $this->abortProcessing($dataSubmitters);
                }
                catch (Exception $ne) {
                    // we do not need to rethrow this exception. We need to preserve and rethrow original exception
                    LogHelper::log_error($ne);
                }

                throw new IllegalStateException($e->getMessage());
            }
            catch (Exception $e) {
                LogHelper::log_error($e->getFile() . ':' . $e->getLine());

                try {
                    $this->abortProcessing($dataSubmitters);
                }
                catch (Exception $ne) {
                    // we do not need to rethrow this exception. We need to preserve and rethrow original exception
                    LogHelper::log_error($ne);
                }

                $ise = new IllegalStateException(
                    ExceptionHelper::getExceptionMessage($e) . t(' [line: @lineNumber]', array('@lineNumber' => $dataProvider->getCurrentLineNumber())),
                    0, $e);
                try {
                    $dataProvider->closeResource();
                }
                catch (Exception $ne) {
                    // we do not need to rethrow this exception. We need to preserve and rethrow original exception
                    LogHelper::log_error($ne);
                }

                throw $ise;
            }

            $dataProvider->closeResource();
        }

        LogHelper::log_notice(t('Parsed @recordCount records', array('@recordCount' => $loadedRecordCount)));

        return $loadedRecordCount;
    }
}
