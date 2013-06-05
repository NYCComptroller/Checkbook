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




class PHPDataSourceHandler extends AbstractNonSQLDataSourceHandler {

    public static $DATASOURCE__TYPE = 'PHP';

    public static $DATASOURCE_NAME__DEFAULT = 'common:PHP';

    protected function getDatasetHandler(DataControllerCallContext $callcontext, $datasetName) {
        $metamodel = data_controller_get_metamodel();

        $dataset = $metamodel->getDataset($datasetName);

        $namespace = NameSpaceHelper::getNameSpace($dataset->datasourceName);
        $classname = $dataset->source;

        $foundScriptName = NULL;

        // TODO load the configuration only once when instance of the class is initialized
        // reuse the cached data here to look for PHP script
        $metamodelConfigurations = module_invoke_all('dc_metamodel');
        foreach ($metamodelConfigurations as $metamodelConfiguration) {
            $path = $metamodelConfiguration['path'];

            $scriptName = "$path/metamodel/metamodel/$namespace/php/" . $classname . '.php';
            if (file_exists($scriptName)) {
                if (isset($foundScriptName)) {
                    throw new IllegalStateException(t(
                    	"Found several PHP scripts to support '@className' dataset: [@previousScriptName, @scriptName]",
                        array('@className' => $classname, '@previousScriptName' => $foundScriptName, '@scriptName' => $scriptName)));
                }

                $foundScriptName = $scriptName;
            }
        }

        if (isset($foundScriptName)) {
            require_once($foundScriptName);
        }
        else {
            throw new IllegalStateException(t("Could not find PHP script to support '@datasetName' dataset", array('@datasetName' => $dataset->publicName)));
        }

        return new $classname;
    }

    public function loadDatasetMetaData(DataControllerCallContext $callcontext, DatasetMetaData $dataset) {
        // TODO review implementation because we pass $dataset now
        $handler = $this->getDatasetHandler($callcontext, $dataset->name);
        $handler->loadDatasetMetaData($callcontext, $dataset);
    }

    public function queryDataset(DataControllerCallContext $callcontext, DatasetQueryRequest $request, ResultFormatter $resultFormatter) {
        $datasetName = $request->getDatasetName();

        $handler = $this->getDatasetHandler($callcontext, $datasetName);

        return $handler->queryDataset($callcontext, $request, $resultFormatter);
    }

    public function countDatasetRecords(DataControllerCallContext $callcontext, DatasetCountRequest $request, ResultFormatter $resultFormatter) {
        $datasetName = $request->getDatasetName();

        $handler = $this->getDatasetHandler($callcontext, $datasetName);

        return $handler->countDatasetRecords($callcontext, $request, $resultFormatter);
    }
}
