<?php
namespace Drupal\data_controller_sql\Datasource\Handler\Impl;

use Drupal\data_controller\Common\Pattern\AbstractObject;
use Drupal\data_controller\MetaModel\MetaData\ColumnMetaData;

abstract class AbstractQueryStatementExecutionCallback extends AbstractObject {

    abstract public function fetchNextRecord($connection, $statement);

    abstract public function getColumnCount($connection, $statement);

    abstract public function getColumnMetaData($connection, $statement, $columnIndex);

    abstract public function calculateApplicationDataType(ColumnMetaData $column);
}
