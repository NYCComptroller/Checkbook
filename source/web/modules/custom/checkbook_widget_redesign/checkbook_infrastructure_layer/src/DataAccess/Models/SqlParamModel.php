<?php

namespace Drupal\checkbook_infrastructure_layer\DataAccess\Models;

use SimpleXMLElement;

class SqlParamModel extends AbstractSqlModel {

  public $name;

  public $type;

  public $required;

  protected static $childElements = NULL;

  function __construct($name, $type, $required) {
    $this->name = $name;
    $this->type = $type;
    $this->required = $required;
  }

  /**
   * @param SimpleXMLElement $xml
   *
   * @return SqlParamModel
   */
  public static function loadFromXml(SimpleXMLElement $xml) {
    $name = (string) $xml->attributes()->name;
    $type = (string) $xml->attributes()->type;
    $required = (string) $xml->attributes()->required;
    return new self($name, $type, $required);
  }

}
