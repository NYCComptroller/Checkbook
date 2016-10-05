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
    public $compareValue;
    public $condition;
    public $xmlString;
    protected static $childElements = array(
        array('xpath'=>'exp','class'=>'SqlExpressionModel')
    );

    /**
     * @param $xmlString
     * @param SqlExpressionModel[] $expressions
     * @param $operator
     * @param $paramName
     * @param $dbField
     * @param $paramValue
     * @param $compareValue
     * @param $condition
     */
    public function __construct($xmlString ,array $expressions, $operator, $paramName, $dbField, $paramValue, $compareValue, $condition) {
        $this->xmlString = $xmlString;
        $this->dbField = $dbField;
        $this->expressions = $expressions;
        $this->operator = $operator;
        $this->paramName = $paramName;
        $this->paramValue = $paramValue;
        $this->compareValue = $compareValue;
        $this->condition = $condition;
        $this->operatorType =
            SqlOperator::isComparisonOperator($operator) ? SqlOperatorType::COMPARISON
            : (SqlOperator::isLogicOperator($operator) ? SqlOperatorType::LOGIC : SqlOperatorType::CONDITION);
    }

    /**
     * @param SimpleXMLElement $xml
     * @return SqlExpressionModel
     */
    public static function loadFromXml(SimpleXMLElement $xml)  {

        $xmlString = $xml->asXML();
        $operator = (string)$xml->attributes()->op;
        $paramName = (string)$xml->attributes()->paramName;
        $dbField = (string)$xml->attributes()->dbField;
        $paramValue = trim((string)$xml);
        $compareValue = (string)$xml->attributes()->compareValue;
        $condition = (string)$xml->attributes()->condition;
        $childModels = self::loadChildXElements($xml);

        return new self($xmlString, $childModels[0], $operator, $paramName, $dbField, $paramValue, $compareValue, $condition);
    }

}