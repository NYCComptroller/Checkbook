<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 3/31/16
 * Time: 2:11 PM
 */

class SqlExpressionModel extends AbstractSqlModel {

    public $expressions = array();
    public $operator;
    public $paramName;
    public $dbField;
    public $paramValue;
    public $operatorType;
    protected static $childElements = array(
        array('xpath'=>'exp','class'=>'SqlExpressionModel')
    );

    /**
     * @param SqlExpressionModel[] $expressions
     * @param $operator
     * @param $paramName
     * @param $dbField
     * @param $paramValue
     */
    public function __construct(array $expressions, $operator, $paramName, $dbField, $paramValue) {
        $this->dbField = $dbField;
        $this->expressions = $expressions;
        $this->operator = $operator;
        $this->paramName = $paramName;
        $this->paramValue = $paramValue;
        $this->operatorType = SqlOperator::isComparisonOperator($operator)
            ? SqlOperatorType::COMPARISON
            : SqlOperatorType::LOGIC;
    }

    /**
     * @param SimpleXMLElement $xml
     * @return SqlExpressionModel
     */
    public static function loadFromXml(SimpleXMLElement $xml)  {

        $operator = (string)$xml->attributes()->op;
        $paramName = (string)$xml->attributes()->paramName;
        $dbField = (string)$xml->attributes()->dbField;
        $paramValue = trim((string)$xml);
        $childModels = self::loadChildXElements($xml);

        return new self($childModels[0], $operator, $paramName, $dbField, $paramValue);
    }

}