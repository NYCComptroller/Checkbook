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


abstract class AbstractCouchDBDataSourceHandler extends AbstractNonSQLDataSourceHandler {

    // the number is still enough to load most of the dictionaries.
    // If you need to load a dictionary which has more records just set 'limit' in your request
    protected static $MAX_LOADED_DOCUMENTS = 10000;
    protected static $SLEEP_TIME_BETWEEN_FAILED_COMMUNICATION_ATTEMPTS = 1; // seconds

    private $converterJson2PHP = NULL;
    private $converterPHP2Json = NULL;

    private $sockets = NULL;

    public function __construct($datasourceType, $extensions) {
        parent::__construct($datasourceType, $extensions);
        $this->converterJson2PHP = new Json2PHPObject(FALSE);
        $this->converterPHP2Json = new PHP2Json();
    }

    public function __destruct() {
        if (isset($this->sockets)) {
            foreach ($this->sockets as $socket) {
                fclose($socket);
            }
        }
        parent::__destruct();
    }

    protected function createInternalDatabase(DataSourceMetaData $datasource, $databaseName) {
        $url = '/' . $databaseName;

        $serverRequest->url = $url;
        $serverRequest->method = 'PUT';
        $serverRequest->authorizationRequired = TRUE;
        // executing the server request
        $serverResponse = $this->communicateWithServer($datasource, $serverRequest);
        $this->checkDocumentExistence($serverResponse, TRUE);
    }

    protected function dropInternalDatabase(DataSourceMetaData $datasource, $databaseName) {
        $url = '/' . $databaseName;

        $serverRequest->url = $url;
        $serverRequest->method = 'DELETE';
        $serverRequest->authorizationRequired = TRUE;
        // executing the server request
        $serverResponse = $this->communicateWithServer($datasource, $serverRequest);
        $this->checkDocumentExistence($serverResponse, TRUE);
    }

    protected function submitDatabaseRecords(DataSourceMetaData $datasource, $databaseName, array &$records) {
        $response = NULL;

        $url = '/' . $databaseName;

        if (count($records) === 1) {
            $serverRequest->url = $url;
            $serverRequest->body = $this->converterPHP2Json->convert($records[0]);
            // executing the server request
            $serverResponseRecord = $this->communicateWithServer($datasource, $serverRequest);

            $this->checkDocumentExistence($serverResponseRecord, TRUE);
            $response[] = $serverResponseRecord;
        }
        else {
            $url .= '/_bulk_docs';

            // TODO consider usage of the following property the future if supported properly
            // $requestBodyObject->all_or_nothing = TRUE;

            $requestBodyObject->docs = $records;
            $requestBody = $this->converterPHP2Json->convert($requestBodyObject);

            $serverRequest->url = $url;
            $serverRequest->body = $requestBody;
            // executing the server request
            $serverResponseRecords = $this->communicateWithServer($datasource, $serverRequest);

            // processing response
            if (is_array($serverResponseRecords)) {
                foreach ($serverResponseRecords as $serverResponseRecord) {
                    $this->checkDocumentExistence($serverResponseRecord, TRUE);
                    $response[] = $serverResponseRecord;
                }
            }
            else {
                $this->checkDocumentExistence($serverResponseRecords, TRUE);
            }
        }

        return $response;
    }

    protected function processRecord(array &$records = NULL, $record, array $columns = NULL, $nestedDocumentBody, ResultFormatter $resultFormatter) {
        // support for 'missing' (error) record
        if ($this->checkDocumentExistence($record, FALSE)) {
            // data body is loaded as an inner node 'doc'
            if ($nestedDocumentBody) {
                $record = $record->doc;
            }

            if (isset($columns)) {
                // removing columns which are not requested
                foreach ($record as $propertyName => $propertyValue) {
                    if (array_search($propertyName, $columns) === FALSE) {
                        unset($record->$propertyName);
                    }

                    // FIXME call $this->resultFormatter->format* functions
                }
            }

            // TODO add support for nested objects and array of objects.
            // 08/13/2010 What did I mean???
            // 09/15/2010 I meant that we can store really complex objects in CouchDB.
            //     Those object may contain nested objects of the object property is an array of other objects.
            //     Such objects might not be supported by our formatters
            if (!$resultFormatter->formatRecord($records, $record)) {
                $records[] = $record;
            }
        }
    }

    protected function applyOrderBy($url, AbstractQueryRequest $request) {
        // TODO support ascending/descending sorting by 'key' column
        // TODO support sorting by any column(s) if there are no 'limit' and 'startWith'
        if (isset($request->sortingConfigurations)) {
            // throw new UnsupportedOperationException(t('Sorting is not supported yet'));
        }

        return array($url, isset($request->sortingConfigurations));
    }

    protected function applyPagination($url, AbstractQueryRequest $request) {
        if (isset($request->startWith) && ($request->startWith > 0)) {
            $url .= ((strpos($url, '?') === FALSE) ? '?' : '&') . "skip=$request->startWith";
        }

        $limit = $request->limit;
        // if keys and limit are not set it could be just a bug. We cannot load all data.
        // The database can be huge. We will load limited (preset) number of records
        if (!isset($limit)) {
            $keys = $this->prepareDocumentIdentifiers($request);
            if (!isset($keys)) {
                $limit = self::$MAX_LOADED_DOCUMENTS;
                LogHelper::log_warn(t(
                	'Loading maximum @limit records for the request: @datasetName',
                    array('@limit' => $limit, '@datasetName' => $request->getDatasetName())));
            }
        }
        if (isset($limit)) {
            $url .= ((strpos($url, '?') === FALSE) ? '?' : '&') . "limit=$limit";
        }

        return $url;
    }

    protected function prepareServerKey(DataSourceMetaData $datasource) {
        return "$datasource->host:$datasource->port";
    }

    public function communicateWithServer(DataSourceMetaData $datasource, $serverRequest) {
        $transmissionResponse = $this->transmitData($datasource, $serverRequest);
        if ($transmissionResponse->status == FALSE) {
            LogHelper::log_error(t(
            	'CouchDB server @host:@port: @error',
                array('@host' => $datasource->host, '@port' => $datasource->port, '@error' => $transmissionResponse->error)));

            // trying for second time with new connection
            $serverKey = $this->prepareServerKey($datasource);
            unset($this->sockets[$serverKey]);

            sleep(self::$SLEEP_TIME_BETWEEN_FAILED_COMMUNICATION_ATTEMPTS);

            $transmissionResponse = $this->transmitData($datasource, $serverRequest);
            if ($transmissionResponse->status == FALSE) {
                LogHelper::log_error(t('CouchDB server @host:@port', array('@host' => $datasource->host, '@port' => $datasource->port)));
                throw new Exception($transmissionResponse->error);
            }
        }

        return $transmissionResponse->result;
    }

    protected function transmitData(DataSourceMetaData $datasource, $serverRequest) {
        $COMMENT_MAX_LENGTH__REQUEST_BODY = 10000;

        $transmissionResponse->status = FALSE;

        $requestURL = $serverRequest->url;
        $requestBody = isset($serverRequest->body) ? $serverRequest->body : NULL;
        $lengthRequestBody = isset($requestBody) ? strlen($requestBody) : 0;

        $httpVersion = '1.1';
        $httpMethod = isset($serverRequest->method)
            ? $serverRequest->method
            : (isset($requestBody) ? 'POST' : 'GET');

        LogHelper::log_info(t('Request: @httpMethod @requestURL', array('@httpMethod' => $httpMethod, '@requestURL' => $requestURL)));
        if (isset($requestBody)) {
            LogHelper::log_debug($requestBody);
        }

        $timeStart = microtime(TRUE);

        // checking / opening connection to the database
        $serverKey = $this->prepareServerKey($datasource);
        $socket = isset($this->sockets[$serverKey]) ? $this->sockets[$serverKey] : NULL;
        if (!isset($socket)) {
            $socket = fsockopen($datasource->host, $datasource->port, $errno, $errstr);
            if ($socket === FALSE) {
                $transmissionResponse->error = t(
                	"Could not connect to the database server: @errorCode-'@errorMessage'",
                    array('@errorCode' => $errno, '@errorMessage' => $errstr));
                return $transmissionResponse;
            }
            else {
                $this->sockets[$serverKey] = $socket;
            }
        }

        // preparing a request
        $request = "$httpMethod $requestURL HTTP/$httpVersion\r\nHost: $datasource->host\r\n";
        if (isset($serverRequest->authorizationRequired) && $serverRequest->authorizationRequired) {
            $request .= 'Authorization: Basic ' . base64_encode($datasource->username . ':' . $datasource->password) . "\r\n";
        }

        $request .= "Connection: Keep-Alive\r\n";
        if (isset($requestBody)) {
            $request .= "Content-Type: application/json\r\n";
            $request .= 'Content-Length: ' . $lengthRequestBody . "\r\n\r\n";
            $request .= $requestBody;
        }

        // sending the request
        $bytesWritten = fwrite($socket, $request . "\r\n");
        if ($bytesWritten === FALSE) {
            $transmissionResponse->error = t('Could not submit the request to the database server');
            return $transmissionResponse;
        }

        // processing response headers
        $headers = NULL;
        while (TRUE) {
            $header = fgets($socket);
            if ($header === FALSE) {
                $transmissionResponse->error = t('Could not read the response header');
                return $transmissionResponse;
            }
            else {
                $header = trim($header);
                if (strlen($header) === 0) {
                    if (isset($headers)) {
                        // it is a delimiter between response header and body
                        break;
                    }
                    else {
                        // empty lines before response header. Should not happen
                    }
                }

                // processing only headers with values (':' is default delimiter)
                if (strpos($header, ':') !== FALSE) {
                    list($key, $value) = explode(':', $header, 2);
                    $headers[strtolower(trim($key))] = trim($value);
                }
            }
        }

        // reading response body (if any)
        $responseBody = '';
        if (isset($headers['transfer-encoding']) && ($headers['transfer-encoding'] == 'chunked')) {
            // chunked response support
            do {
                $bytesToRead = 0;

                $line = fgets($socket);
                if ($line === FALSE) {
                    $transmissionResponse->error = t('Could not read the response chunk size');
                    return $transmissionResponse;
                }

                $line = rtrim($line);

                if (preg_match('(^([0-9a-f]+)(?:;.*)?$)', $line, $match)) {
                    $bytesToRead = hexdec($match[1]);

                    $bytesLeft = $bytesToRead;
                    while ($bytesLeft > 0) {
                        $read = fread($socket, $bytesLeft + 2);
                        if ($read === FALSE) {
                            $transmissionResponse->error = t(
                            	'Could not read the whole response. @bytesLeft bytes left',
                                array('@bytesLeft' => $bytesLeft));
                            return $transmissionResponse;
                        }
                        else {
                            $responseBody .= $read;
                            $bytesLeft -= strlen($read);
                        }
                    }
                }
            } while ($bytesToRead > 0);
        }
        else {
            // Non-chunked response support
            $bytesToRead = (isset($headers['content-length']) ? (int) $headers['content-length'] : NULL);
            while (!isset($bytesToRead) || ($bytesToRead > 0)) {
                $read = isset($bytesToRead) ? fgets($socket, $bytesToRead + 1) : fgets($socket);

                if ($read === FALSE) {
                    if (isset($bytesToRead) && ($bytesToRead > 0)) {
                        $transmissionResponse->error = t(
                        	'Could not read the whole response. $bytesToRead bytes left',
                            array('@bytesToRead' => $bytesToRead));
                        return $transmissionResponse;
                    }
                    else {
                        break;
                    }
                }
                else {
                    $responseBody .= $read;
                    if (isset($bytesToRead)) {
                        $bytesToRead -= strlen($read);
                    }
                }
            }
        }

        LogHelper::log_info(t('Database execution time: !executionTime', array('!executionTime' => ExecutionPerformanceHelper::formatExecutionTime($timeStart))));

        $responseBodyObject = $this->converterJson2PHP->convert($responseBody);
        if (!isset($responseBodyObject)) {
            $error = t('Database server did not provide parsable response body');
            LogHelper::log_error($error);
            LogHelper::log_error(t('Response headers:'));
            LogHelper::log_error($headers);
            LogHelper::log_error(t('Response body:'));
            LogHelper::log_error($responseBody);

            throw new Exception($error);
        }

        $transmissionResponse->status = TRUE;
        $transmissionResponse->result = $responseBodyObject;

        return $transmissionResponse;
    }

    protected function checkDocumentExistence($record, $isExceptionThrown) {
        $wasDocumentProcessed = !isset($record->error);

        if (!$wasDocumentProcessed) {
            $message = t(
                'Error processing record@id: @error@errorReason',
                array('@id' => (isset($record->id) ? " (id: '$record->id')" : (isset($record->key) ? " (key: '$record->key')" : '')),
                      '@error' => $record->error,
                      '@errorReason' => (isset($record->reason) ? " - \"$record->reason\"" : '')));

            LogHelper::log_error($record);
            if ($isExceptionThrown) {
                throw new Exception($message);
            }
        }

        return $wasDocumentProcessed;
    }

    // FIXME remove this function and use formatValue implemented in parent class
    public function formatValue($datatype, $value) {
        if (!isset($value)) {
            return 'null';
        }

        // data type specific adjustments
        $datatypeHandler = DataTypeFactory::getInstance()->getHandler($datatype);
        $formattedValue = $datatypeHandler->castValue($value);

        // CouchDB-driven adjustments
        switch ($datatypeHandler->getStorageDataType()) {
            case StringDataTypeHandler::$DATA_TYPE:
            case DateDataTypeHandler::$DATA_TYPE:
            case TimeDataTypeHandler::$DATA_TYPE:
            case DateTimeDataTypeHandler::$DATA_TYPE:
                $formattedValue = '"' . $value . '"';
                break;
            case IntegerDataTypeHandler::$DATA_TYPE:
            case NumberDataTypeHandler::$DATA_TYPE:
            case CurrencyDataTypeHandler::$DATA_TYPE:
            case PercentDataTypeHandler::$DATA_TYPE:
                break;
            case BooleanDataTypeHandler::$DATA_TYPE:
                break;
            default:
                throw new UnsupportedOperationException(t(
                	"Unsupported data type '@datatype' to format the value: @value",
                    array('@datatype' => $datatype, '@value' => $value)));
        }

        return $formattedValue;
    }
}
