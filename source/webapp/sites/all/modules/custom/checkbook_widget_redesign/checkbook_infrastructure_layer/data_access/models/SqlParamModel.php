<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 3/31/16
 * Time: 12:04 PM
 */

class SqlParamModel extends AbstractSqlModel {

    public $name;
    public $required;
    protected static $childElements = null;

    function __construct($name, $required) {
        $this->name = $name;
        $this->required = $required;
    }

    /**
     * @param SimpleXMLElement $xml
     * @return SqlParamModel
     */
    public static function loadFromXml(SimpleXMLElement $xml)  {
        $name = (string)$xml->attributes()->name;
        $required = (string)$xml->attributes()->datasource;
        return new self($name, $required);
    }
}