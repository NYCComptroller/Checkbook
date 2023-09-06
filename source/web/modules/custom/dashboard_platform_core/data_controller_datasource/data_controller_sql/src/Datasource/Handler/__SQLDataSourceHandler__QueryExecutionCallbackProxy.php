<?php
/**
 * This file is part of the Checkbook NYC financial transparency software.
 *
 * Copyright (c) 2012 – 2023 New York City
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Drupal\data_controller_sql\Datasource\Handler;

use Drupal\data_controller\Common\Datatype\DataTypeFactory;
use Drupal\data_controller\Controller\CallContext\DataControllerCallContext;
use Drupal\data_controller\Datasource\Formatter\ResultFormatter;
use Drupal\data_controller_sql\Datasource\Handler\Impl\AbstractQueryStatementExecutionCallback;

/**
 * Class __SQLDataSourceHandler__QueryExecutionCallbackProxy
 */
class __SQLDataSourceHandler__QueryExecutionCallbackProxy extends __SQLDataSourceHandler__AbstractQueryCallbackProxy
{

  /**
   * @var ResultFormatter|null
   */
  private $resultFormatter = NULL;

  /**
   * __SQLDataSourceHandler__QueryExecutionCallbackProxy constructor.
   * @param AbstractQueryStatementExecutionCallback $callback
   * @param ResultFormatter $resultFormatter
   */
  public function __construct(AbstractQueryStatementExecutionCallback $callback, ResultFormatter $resultFormatter)
  {
    parent::__construct($callback);
    $this->resultFormatter = $resultFormatter;
  }

  /**
   * @param DataControllerCallContext $callcontext
   * @param $connection
   * @param $statement
   * @return array|mixed|null
   * @throws IllegalArgumentException
   */
  public function callback(DataControllerCallContext $callcontext, $connection, $statement)
  {
    $records = NULL;

    $datatypeFactory = DataTypeFactory::getInstance();

    $dataset = $this->prepareMetaData($callcontext, $connection, $statement);

    while ($record = $this->callback->fetchNextRecord($connection, $statement)) {
      // post-processing the record
      $adjustedRecord = NULL;
      foreach ($dataset->columns as $column) {
        $columnValue = $record[$column->columnIndex];
        $propertyValue = $datatypeFactory->getHandler($column->type->applicationType)->castValue($columnValue);

        $this->resultFormatter->setRecordPropertyValue($adjustedRecord, $column->alias, $propertyValue);
      }

      if (!$this->resultFormatter->formatRecord($records, $adjustedRecord)) {
        $records[] = $adjustedRecord;
      }
    }
    $this->resultFormatter->postFormatRecords($records);

    return $records;
  }
}
