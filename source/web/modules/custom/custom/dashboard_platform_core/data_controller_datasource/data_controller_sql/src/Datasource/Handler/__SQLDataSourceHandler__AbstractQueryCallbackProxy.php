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

use Drupal\data_controller\Common\Object\Exception\IllegalArgumentException;
use Drupal\data_controller\Common\Pattern\AbstractObject;
use Drupal\data_controller\Controller\CallContext\DataControllerCallContext;
use Drupal\data_controller\MetaModel\Definition\DataSet\DatasetSystemColumnNames;
use Drupal\data_controller\MetaModel\MetaData\DatasetMetaData;
use Drupal\data_controller_sql\Datasource\Handler\Impl\AbstractQueryStatementExecutionCallback;

/**
 * Class __SQLDataSourceHandler__AbstractQueryCallbackProxy
 */
abstract class __SQLDataSourceHandler__AbstractQueryCallbackProxy extends AbstractObject
{

  /**
   * @var AbstractQueryStatementExecutionCallback|null
   */
  protected $callback = NULL;

  /**
   * __SQLDataSourceHandler__AbstractQueryCallbackProxy constructor.
   * @param AbstractQueryStatementExecutionCallback $callback
   */
  public function __construct(AbstractQueryStatementExecutionCallback $callback)
  {
    parent::__construct();
    $this->callback = $callback;
  }

  /**
   * @param DataControllerCallContext $callcontext
   * @param $connection
   * @param $statement
   * @return DatasetMetaData
   * @throws IllegalArgumentException
   */
  public function prepareMetaData(DataControllerCallContext $callcontext, $connection, $statement)
  {
    $dataset = new DatasetMetaData();

    for ($i = 0, $columnCount = $this->callback->getColumnCount($connection, $statement); $i < $columnCount; $i++) {
      $column = $this->callback->getColumnMetaData($connection, $statement, $i);
      if ($column === FALSE) {
        throw new IllegalArgumentException(t('The column with the index does not exist: @columnIndex', array('@columnIndex' => $i)));
      }

      $column->name = strtolower($column->name);
      $column->columnIndex = $i;
      $column->type->applicationType = $this->callback->calculateApplicationDataType($column);

      // support for column mapping
      $column->alias = $callcontext->columnMapping[$column->name] ?? $column->name;

      // checking if the column is a system column which should be invisible
      if (substr_compare($column->name, DatasetSystemColumnNames::COLUMN_NAME_PREFIX, 0, strlen(DatasetSystemColumnNames::COLUMN_NAME_PREFIX)) === 0) {
        $column->visible = FALSE;
      }

      $dataset->registerColumnInstance($column);
    }

    return $dataset;

  }

  /**
   * @param DataControllerCallContext $callcontext
   * @param $connection
   * @param $statement
   * @return mixed
   */
  abstract public function callback(DataControllerCallContext $callcontext, $connection, $statement);
}
