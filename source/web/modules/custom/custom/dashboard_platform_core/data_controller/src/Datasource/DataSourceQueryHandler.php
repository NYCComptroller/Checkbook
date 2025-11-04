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

namespace Drupal\data_controller\Datasource;

use Drupal\data_controller\Controller\CallContext\DataControllerCallContext;
use Drupal\data_controller\Datasource\Formatter\ResultFormatter;
use Drupal\data_controller\Datasource\Request\Cube\CubeQueryRequest;
use Drupal\data_controller\Datasource\Request\Dataset\DatasetCountRequest;
use Drupal\data_controller\Datasource\Request\Dataset\DatasetQueryRequest;
use Drupal\data_controller\Datasource\Request\Sequence\SequenceRequest;
use Drupal\data_controller\MetaModel\MetaData\CubeMetaData;
use Drupal\data_controller\MetaModel\MetaData\DatasetMetaData;

interface DataSourceQueryHandler extends DataSourceHandler {

    function isJoinSupported($datasourceNameA, $datasourceNameB);

    function loadDatasetMetaData(DataControllerCallContext $callcontext, DatasetMetaData $dataset);
    function prepareCubeMetaData(DataControllerCallContext $callcontext, CubeMetaData $cube);

    function getNextSequenceValues(DataControllerCallContext $callcontext, SequenceRequest $request);

    function queryDataset(DataControllerCallContext $callcontext, DatasetQueryRequest $request, ResultFormatter $resultFormatter);
    function countDatasetRecords(DataControllerCallContext $callcontext, DatasetCountRequest $request, ResultFormatter $resultFormatter);

    function queryCube(DataControllerCallContext $callcontext, CubeQueryRequest $request, ResultFormatter $resultFormatter);
    function countCubeRecords(DataControllerCallContext $callcontext, CubeQueryRequest $request, ResultFormatter $resultFormatter);
}
