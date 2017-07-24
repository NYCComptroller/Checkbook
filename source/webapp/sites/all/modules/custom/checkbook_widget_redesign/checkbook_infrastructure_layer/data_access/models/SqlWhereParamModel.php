<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 3/31/16
 * Time: 12:13 PM
 */

class SqlWhereParamModel extends AbstractSqlModel {

    public $expressions = array();
    protected static $childElements = array(
        array('xpath'=>'exp','class'=>'SqlExpressionModel')
    );

    /**
     * @param SqlExpressionModel[] $expressions
     */
    function __construct(array $expressions) {
        $this->expressions = $expressions;
    }

    /**
     * @param SimpleXMLElement $xml
     * @return SqlWhereParamModel
     */
    public static function loadFromXml(SimpleXMLElement $xml)  {
        $childModels = self::loadChildXElements($xml);
        return new self($childModels[0]);
    }
}