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

namespace Drupal\data_controller_sql\Datasource\Dataset\Assembler\Statement;

use Drupal\data_controller\Common\Parameter\ReferencePathHelper;
use Drupal\data_controller\Datasource\DataSourceQueryFactory;
use Drupal\data_controller\MetaModel\MetaData\DatasetMetaData;

class DatasetSection extends TableSection {

  public $dataset = NULL;

  public function __construct(DatasetMetaData $dataset, $alias = NULL) {
    parent::__construct($dataset->source, $alias);
    $this->dataset = $dataset;
  }

  protected function assembleTableName() {
    $environment_metamodel = data_controller_get_environment_metamodel();

    $datasourceName = $this->dataset->datasourceName;
    $datasource = $environment_metamodel->getDataSource($datasourceName);

    $datasourceQueryHandler = DataSourceQueryFactory::getInstance()->getHandler($datasource->type);

    $owner = $datasourceQueryHandler->getDataSourceOwner($datasourceName);

    return (isset($owner) ? ($owner . '.') : '') . $this->name;
  }

  public function findColumn($columnName) {
    list($referencedDatasetName, $referencedColumnName) = ReferencePathHelper::splitReference($columnName);

    return isset($referencedDatasetName)
      ? (($this->dataset->name == $referencedDatasetName) ? parent::findColumn($referencedColumnName) : NULL)
      : parent::findColumn($referencedColumnName);
  }
}
