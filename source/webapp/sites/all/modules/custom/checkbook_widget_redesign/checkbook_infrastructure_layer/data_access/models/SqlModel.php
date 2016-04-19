<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 3/30/16
 * Time: 6:36 PM
 */


class SqlModel extends AbstractSqlModel {

    private $statements = array();
    protected static $childElements = array(
        array('xpath'=>'statement','class'=>'SqlStatementModel')
    );

    /**
     * @param ISqlModel[] $statements
     */
    function __construct(array $statements) {
        $this->statements = $statements;
    }

    /**
     * @param SimpleXMLElement $xml
     * @return SqlModel
     */
    public static function loadFromXml(SimpleXMLElement $xml)
    {
        $childModels = self::loadChildXElements($xml);
        return new self($childModels[0]);
    }

     /**
     * @param $name
     * @return SqlStatementModel
     */
    public function getStatement($name) {
        foreach($this->statements as $statement) {
            if($statement->name == $name)
                return $statement;
        }
    }

    /**
     * @return SqlStatementModel[]
     */
    public function getStatements() {
        return $this->statements;
    }
}