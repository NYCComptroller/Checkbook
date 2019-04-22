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


abstract class AbstractDataController extends AbstractObject implements DataController {

    /**
     * @return DataControllerCallContext
     */
    protected function prepareCallContext() {
        return new DataControllerCallContext();
    }

    /**
     * @param string $datasetName
     * @return DataSourceHandler
     */
    protected function getDataSourceHandlerByDatasetName($datasetName) {
        $metamodel = data_controller_get_metamodel();

        $dataset = $metamodel->getDataset($datasetName);

        if (!is_array($dataset->columns) || !sizeof($dataset->columns)) {
          LogHelper::log_debug(t('Could not load metadata for dataset @datasourceName ; Query: @source', [
            '@datasourceName' => $dataset->datasourceName,
            '@source' => $dataset->source
          ]));
        }

        return $this->getDataSourceHandlerByDataset($dataset);
    }

    /**
     * @param DatasetMetaData $dataset
     * @return DataSourceHandler
     */
    protected function getDataSourceHandlerByDataset(DatasetMetaData $dataset) {
        $environment_metamodel = data_controller_get_environment_metamodel();

        $datasource = $environment_metamodel->getDataSource($dataset->datasourceName);

        return $this->getDataSourceHandler($datasource->name);
    }

    /**
     * @param string $datasourceName
     * @return DataSourceHandler
     */
    protected function getDataSourceHandler($datasourceName) {
        $environment_metamodel = data_controller_get_environment_metamodel();

        $datasource = $environment_metamodel->getDataSource($datasourceName);

        return $this->lookupDataSourceHandler($datasource->type);
    }

    /**
     * @param string $type
     * @return DataSourceHandler
     */
    abstract protected function lookupDataSourceHandler($type);
}
