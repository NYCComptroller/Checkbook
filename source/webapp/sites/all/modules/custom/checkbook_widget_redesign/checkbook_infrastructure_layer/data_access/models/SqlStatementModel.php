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
    public $sql;
    public $parameters = array();
    public $whereParams = array();
    public $expressions = array();
    public $groupBy;
    public $having;
    public $query;
    public $countQuery;

    protected static $childElements = array(
        array('xpath'=>'param','class'=>'SqlParamModel'),
        array('xpath'=>'sql/where','class'=>'SqlWhereParamModel'),
        array('xpath'=>'sql/exp','class'=>'SqlExpressionModel')
    );

    /**
     * @param $name
     * @param $datasource
     * @param $sql
     * @param ISqlModel[] $parameters
     * @param ISqlModel[] $whereParams
     * @param ISqlModel[] $expressions
     * @param $groupBy
     * @param $having
     */
    function __construct($name, $datasource, $sql, $parameters, $whereParams, $expressions, $groupBy, $having) {
        $this->name = $name;
        $this->datasource = $datasource;
        $this->sql = $sql;
        $this->parameters = $parameters;
        $this->whereParams = $whereParams;
        $this->expressions = $expressions;
        $this->groupBy = $groupBy;
        $this->having = $having;
    }

    /**
     * @param SimpleXMLElement $xml
     * @return SqlStatementModel
     */
    public static function loadFromXml(SimpleXMLElement $xml)  {

        $name = (string)$xml->attributes()->name;
        $datasource = (string)$xml->attributes()->datasource;
        $sql = strip_tags(trim($xml->sql->asXml()),'<where><exp><groupBy><having>');
        $groupBy = trim((string)$xml->sql->groupBy);
        $having = trim((string)$xml->sql->having);
        $childModels = self::loadChildXElements($xml);

        return new self($name,$datasource,$sql,$childModels[0],$childModels[1],$childModels[2],$groupBy,$having);
    }
}