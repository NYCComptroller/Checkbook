<?php

namespace Drupal\checkbook_infrastructure_layer\DataAccess\Models;

use SimpleXMLElement;

class SqlModel extends AbstractSqlModel {

  private $statements = [];

  protected static $childElements = [
    ['xpath' => 'statement', 'class' => 'SqlStatementModel'],
  ];

  /**
   * @param ISqlModel[] $statements
   */
  function __construct(array $statements) {
    $this->statements = $statements;
  }

  /**
   * @param SimpleXMLElement $xml
   *
   * @return SqlModel
   */
  public static function loadFromXml(SimpleXMLElement $xml) {
    $childModels = self::loadChildXElements($xml);
    return new self($childModels[0]);
  }

  /**
   * @param $name
   *
   * @return SqlStatementModel
   */
  public function getStatement($name) {
    foreach ($this->statements as $statement) {
      if ($statement->name == $name) {
        return $statement;
      }
    }
  }

  /**
   * @return SqlStatementModel[]
   */
  public function getStatements() {
    return $this->statements;
  }

}
