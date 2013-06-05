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
 * XML Formatter class
 */
class XMLFormatter extends AbstractFormatter {
  private $includeRootElement;

  /**
   * @param $data_records
   * @param $response_columns
   * @param $configuration
   * @param bool $include_root_element
   */
  function __construct($data_records, $response_columns, $configuration, $include_root_element = FALSE) {
    parent::__construct($data_records, $response_columns, $configuration);
    $this->includeRootElement = $include_root_element;
  }

  /**
   * @return mixed|null
   */
  function formatData() {
    if (empty($this->dataRecords)) {
      return NULL;
    }

    $document = new DOMDocument();
    $document->preserveWhiteSpace = FALSE;

    $root_element = NULL;
    if (isset($this->configuration->rootElement) && $this->includeRootElement) {
      $root_element = $document->appendChild($document->createElement($this->configuration->rootElement));
    }

    $row_parent_element = $document->createElement($this->configuration->rowParentElement);
    $row_elements = $this->configuration->rowElements;
    $row_elements_column = get_object_vars($this->configuration->elementsColumn);
    if (isset($this->configuration->rowDataGroupElements)) {
      $row_data_group_elements = get_object_vars($this->configuration->rowDataGroupElements);
    }

    foreach ($this->dataRecords as $data_record) {
      // Process Row Elements:
      $record_parent_element = clone $row_parent_element;

      foreach ($row_elements as $row_element_name) {
        if (in_array($row_element_name, $this->responseColumns)) {
          $this->addElement($record_parent_element, $document, $row_element_name, $data_record[$row_elements_column[$row_element_name]]);
        }
      }

      // Process row data group elements:
      if (isset($row_data_group_elements)) {
        foreach ($row_data_group_elements as $row_data_group_parent_element_name => $row_data_group_element_names) {
          $record_data_group_parent_element = $document->createElement($row_data_group_parent_element_name);
          foreach ($row_data_group_element_names as $row_data_group_element_name) {
            if (in_array($row_data_group_element_name, $this->responseColumns)) {
              $this->addElement($record_data_group_parent_element, $document, $row_data_group_element_name, $data_record[$row_elements_column[$row_data_group_element_name]]);
            }
          }
          if ($record_data_group_parent_element->hasChildNodes()) {
            $record_parent_element->appendChild($record_data_group_parent_element);
          }
        }
      }

      if ($record_parent_element->hasChildNodes()) {
        if (isset($root_element)) {
          $root_element->appendChild($record_parent_element);
        }
        else {
          $document->appendChild($record_parent_element);
        }
      }

    }

    $document_string = $this->getDocumentString($document);
    $document = NULL;
    return $document_string;
  }

  /**
   * @param $document
   * @return mixed|null
   */
  private function getDocumentString($document) {
    // TODO - current 'LIBXML_NOXMLDECL' is not working.
    $document_str = ($document != NULL && $document->hasChildNodes()) ? $document->saveXML(NULL, LIBXML_NOXMLDECL) : NULL;

    if (isset($document_str)) {
      $document_str = str_replace('<?xml version="1.0"?>', '', $document_str);
    }

    return $document_str;
  }

  /**
   * @param $result
   * @param $document
   * @param $element_name
   * @param $value
   */
  private function addElement($result, $document, $element_name, $value) {
    // if(isset($value)){
    $result->appendChild($document->createElement($element_name, (isset($value) ? self::escapeXmlCharacters($value) : '')));
    // }
  }

  /**
   * @static
   * @param $value
   * @return string
   */
  private static function escapeXmlCharacters($value) {
    return check_plain($value);
  }
}
