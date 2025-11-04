<?php

namespace Drupal\checkbook_infrastructure_layer\DataAccess\Models;

use SimpleXMLElement;

interface ISqlModel {

  /**
   * @param SimpleXMLElement $xml
   *
   * @return SqlExpressionModel
   */
  public static function loadFromXml(SimpleXMLElement $xml);

  /**
   * @param SimpleXMLElement $xml
   *
   * @return ISqlModel[]
   */
  public static function loadChildXElements(SimpleXMLElement $xml);

}
