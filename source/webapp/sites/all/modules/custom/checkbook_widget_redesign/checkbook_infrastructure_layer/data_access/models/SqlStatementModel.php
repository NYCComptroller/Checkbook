<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 3/30/16
 * Time: 6:37 PM
 */


class SqlStatementModel extends AbstractSqlModel {

    public $name;
    public $datasource;
    public $query;

    public $params = array();
    public $select;
    public $whereParams = array();

    protected static $childElements = array(
        array('xpath'=>'param','class'=>'SqlParamModel'),
        array('xpath'=>'sql/where','class'=>'SqlWhereParamModel')
    );

    /**
     * @param $name
     * @param $datasource
     * @param $select
     * @param SqlParamModel[] $params
     * @param SqlWhereParamModel[] $whereParams
     */
    function __construct($name, $datasource, $select, array $params, array $whereParams) {
        $this->name = $name;
        $this->datasource = $datasource;
        $this->select = $select;
        $this->parameters = $params;
        $this->whereParams = $whereParams;
    }

    /**
     * @param SimpleXMLElement $xml
     * @return SqlStatementModel
     */
    public static function loadFromXml(SimpleXMLElement $xml)  {

        $name = (string)$xml->attributes()->name;
        $datasource = (string)$xml->attributes()->datasource;
        $select = trim((string)$xml->sql->select);
        $childModels = self::loadChildXElements($xml);

        return new self($name,$datasource,$select,$childModels[0],$childModels[1]);
    }

    /**
     * @param SqlParamModel $param
     */
    public function addParameter(SqlParamModel $param) {
        $this->params[] = $param;
    }

    /**
     * @return SqlParamModel[]
     */
    public function getParameters() {
        return $this->params;
    }
}