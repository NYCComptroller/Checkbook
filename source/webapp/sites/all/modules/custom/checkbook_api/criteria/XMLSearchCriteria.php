<?php
/**
* This file is part of the Checkbook NYC financial transparency software.
* 
* Copyright (C) 2012, 2013 New York City
* 
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU Affero General Public License as
* published by the Free Software Foundation, either version 3 of the
* License, or (at your option) any later version.
* 
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU Affero General Public License for more details.
* 
* You should have received a copy of the GNU Affero General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


/**
 * Class to construct to XML requests
 */
class XMLSearchCriteria extends AbstractAPISearchCriteria {
  protected $requestNode;

  /**
   * @param $request_node
   */
  function __construct($request_node) {
    $this->requestNode = $request_node;
    $this->setRequestCriteria();
  }

  /**
   *
   */
  protected function setRequestCriteria() {
    // $obj = simplexml_load_string($this->requestNode->saveXML($this->requestNode));
    $xpath = new DOMXpath($this->requestNode);

    $this->validateCriteriaElements($xpath->query("//search_criteria/criteria"));
    $this->setGlobalCriteria($this->requestNode);
    $this->setValueCriteria($xpath->query("//search_criteria/criteria[type/text() = 'value']"));
    $this->setRangeCriteria($xpath->query("//search_criteria/criteria[type/text() = 'range']"));
    $this->setResponseColumnsCriteria($xpath->query("//response_columns"));

    $this->criteria['global']['response_format'] = 'xml';
  }

  /**
   * @param $criteria_element_nodes
   */
  private function validateCriteriaElements($criteria_element_nodes) {
    if (!is_null($criteria_element_nodes)) {
      $cnt = 0;
      foreach ($criteria_element_nodes as $criteria_element_node) {
        if ($criteria_element_node->nodeType == XML_ELEMENT_NODE) {
          $cnt++;
          foreach ($criteria_element_node->childNodes as $criteria_node) {
            if ($criteria_node->nodeType == XML_ELEMENT_NODE) {
              switch ($criteria_node->localName) {
                case "type":
                  $value = $criteria_node->nodeValue;
                  if ($value != 'value' && $value != 'range') {
                    $this->addError(1107, array('@number' => $cnt, '@typeValue' => $value));
                  }
                  break;
              }
            }
          }
        }
      }
    }
  }

  /**
   * @param $node
   */
  private function setGlobalCriteria($node) {
    if (!is_null($node)) {
      foreach ($node->childNodes as $child_node) {
        if ($child_node->nodeType == XML_ELEMENT_NODE) {
          switch ($child_node->localName) {
            case "request":
              $this->setGlobalCriteria($child_node);
              break;

            case "type_of_data":
              $this->criteria['global']['type_of_data'] = $child_node->nodeValue;
              break;

            case "records_from":
              $this->criteria['global']['records_from'] = $child_node->nodeValue;
              break;

            case "max_records":
              $this->criteria['global']['max_records'] = $child_node->nodeValue;
              break;
          }
        }
      }
    }
  }

  /**
   * @param $criteria_elements
   */
  private function setValueCriteria($criteria_elements) {

    if (!is_null($criteria_elements)) {
      foreach ($criteria_elements as $criteria_element) {
        if ($criteria_element->hasChildNodes()) {
          $name = NULL;
          $value = NULL;
          foreach ($criteria_element->childNodes as $child_node) {
            if ($child_node->nodeType == XML_ELEMENT_NODE && $child_node->localName == 'name') {
              $name = $child_node->nodeValue;
            }
            else {
              if ($child_node->nodeType == XML_ELEMENT_NODE && $child_node->localName == 'value') {
                $value = $child_node->nodeValue;
              }
            }
          }

          $this->criteria['value'][$name] = $value;
        }
      }
    }

  }

  /**
   * @param $criteria_elements
   */
  private function setRangeCriteria($criteria_elements) {

    if (!is_null($criteria_elements)) {
      foreach ($criteria_elements as $criteria_element) {
        if ($criteria_element->hasChildNodes()) {
          $name = NULL;
          $range_start = NULL;
          $range_end = NULL;
          foreach ($criteria_element->childNodes as $child_node) {
            if ($child_node->nodeType == XML_ELEMENT_NODE) {
              switch ($child_node->localName) {
                case "name":
                  $name = $child_node->nodeValue;
                  break;

                case "start":
                  $range_start = $child_node->nodeValue;
                  break;

                case "end":
                  $range_end = $child_node->nodeValue;
                  break;
              }
            }
            if (isset($name)) {
              $this->criteria['range'][$name] = array($range_start, $range_end);
            }
          }
        }
      }
    }

  }

  /**
   * @param $response_column_elements
   */
  private function setResponseColumnsCriteria($response_column_elements) {

    if (!is_null($response_column_elements)) {
      foreach ($response_column_elements as $response_column_element) {
        if ($response_column_element->hasChildNodes()) {
          foreach ($response_column_element->childNodes as $child_node) {
            if ($child_node->nodeType == XML_ELEMENT_NODE && $child_node->localName == 'column'
              && !empty($child_node->nodeValue)
            ) {
              $this->criteria['responseColumns'][] = $child_node->nodeValue;
            }
          }
        }
      }
    }

  }

  /**
   * @return mixed
   */
  function getRequest() {
    return $this->requestNode;
  }

  /**
   * @return int|mixed
   */
  function getMaxAllowedTransactionResults() {
    return 1000;
  }
}
