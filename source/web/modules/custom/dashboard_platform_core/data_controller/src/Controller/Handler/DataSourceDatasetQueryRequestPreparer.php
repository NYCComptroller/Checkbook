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

namespace Drupal\data_controller\Controller\Handler;

use Drupal\data_controller\Common\Pattern\AbstractObject;
use Drupal\data_controller\Controller\Request\DataQueryControllerDatasetRequest;
use Drupal\data_controller\Datasource\Request\Dataset\DatasetCountRequest;
use Drupal\data_controller\Datasource\Request\Dataset\DatasetQueryRequest;


class DataSourceDatasetQueryRequestPreparer extends AbstractObject {

    public function prepareDatasetQueryRequest(DataQueryControllerDatasetRequest $request) {
        $datasourceRequest = new DatasetQueryRequest($request->datasetName);

        $datasourceRequest->addCompositeQueryValues($request->parameters);
        $datasourceRequest->addColumns($request->columns);
        $datasourceRequest->addOrderByColumns($request->orderBy,$request->sortSourceByNull);
        $datasourceRequest->setPagination($request->limit, $request->startWith);
        $datasourceRequest->addLogicalOrColumns($request->logicalOrColumns);

        return $datasourceRequest;
    }

    public function prepareDatasetCountRequest(DataQueryControllerDatasetRequest $request) {
        $datasourceRequest = new DatasetCountRequest($request->datasetName);

        $datasourceRequest->addCompositeQueryValues($request->parameters);
        $datasourceRequest->addLogicalOrColumns($request->logicalOrColumns);

        return $datasourceRequest;
    }
}
