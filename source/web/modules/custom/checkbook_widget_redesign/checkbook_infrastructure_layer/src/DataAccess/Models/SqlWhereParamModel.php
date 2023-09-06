<?php

namespace Drupal\checkbook_infrastructure_layer\DataAccess\Models;

use SimpleXMLElement;

class SqlWhereParamModel extends AbstractSqlModel {

  public $expressions = [];

  protected static $childElements = [
    ['xpath' => 'exp', 'class' => 'SqlExpressionModel'],
  ];

  /**
   * @param SqlExpressionModel[] $expressions
   */
  function __construct(array $expressions) {
    $this->expressions = $expressions;
  }

  /**
   * @param SimpleXMLElement $xml
   *
   * @return SqlWhereParamModel
   */
  public static function loadFromXml(SimpleXMLElement $xml) {
    $childModels = self::loadChildXElements($xml);
    return new self($childModels[0]);
  }

}
