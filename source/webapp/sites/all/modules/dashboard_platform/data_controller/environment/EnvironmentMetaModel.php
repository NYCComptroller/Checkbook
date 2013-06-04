<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




class EnvironmentMetaModel extends AbstractMetaModel {

    /**
     * @var DataSourceMetaData[]
     */
    public $datasources = array();

    public function __clone() {
        parent::__clone();

        $this->datasources = ArrayHelper::cloneArray($this->datasources);
    }

    protected function finalize() {
        parent::finalize();

        $this->finalizeDataSources($this->datasources);
    }

    protected function validate() {
        parent::validate();

        $this->validateDataSources($this->datasources);
    }

    public function findDataSource($datasourceName) {
        return isset($this->datasources[$datasourceName])
            ? $this->datasources[$datasourceName]
            : NULL;
    }

    /**
     * @param $datasourceName
     * @return DataSourceMetaData
     */
    public function getDataSource($datasourceName) {
        $datasource = $this->findDataSource($datasourceName);
        if (!isset($datasource)) {
            $this->errorDataSourceNotFound($datasourceName);
        }

        return $datasource;
    }

    public function findDataSourceByNamespacelessName($datasourceNamespacelessName) {
        $datasources = NULL;

        foreach ($this->datasources as $datasourceName => $datasource) {
            list($namespace, $datasourceNameOnly) = NameSpaceHelper::splitAlias($datasourceName);
            if ($datasourceNameOnly == $datasourceNamespacelessName) {
                $datasources[$datasourceName] = $datasource;
            }
        }

        return $datasources;
    }

    public function getDataSources() {
        return $this->datasources;
    }

    protected function finalizeDataSources(array &$datasources) {
        foreach ($datasources as $datasource) {
            $datasource->finalize();
        }
    }

    protected function validateDataSource(DataSourceMetaData $datasource) {}

    protected function validateDataSources(array &$datasources) {
        foreach ($datasources as $datasource) {
            $this->validateDataSource($datasource);
        }
    }

    public function registerDataSource(DataSourceMetaData $datasource) {
        $this->checkAssemblingStarted();

        if (!isset($datasource->name)) {
            throw new IllegalArgumentException(t('DataSource name has not been defined'));
        }

        $datasourceName = $datasource->name;

        if (isset($this->datasources[$datasourceName])) {
            throw new IllegalArgumentException(t(
            	"DataSource with name '@datasourceName' has already been defined",
            	array('@datasourceName' => $datasource->publicName)));
        }

        $this->datasources[$datasourceName] = $datasource;
    }

    public function unregisterDataSource($datasourceName) {
        $this->checkAssemblingStarted();

        if (!isset($this->datasources[$datasourceName])) {
            $this->errorDataSourceNotFound($datasourceName);
        }

        $datasource = $this->datasources[$datasourceName];

        unset($this->datasources[$datasourceName]);

        return $datasource;
    }

    protected function errorDataSourceNotFound($datasourceName) {
        throw new IllegalArgumentException(t("Could not find '@datasourceName' data source definition", array('@datasourceName' => $datasourceName)));
    }
}
