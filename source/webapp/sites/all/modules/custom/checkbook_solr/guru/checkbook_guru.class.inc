<?php
/**
 * This file is part of the Checkbook NYC financial transparency software.
 *
 * Copyright (C) 2019 New York City
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
 * Class CheckbookGuru
 */
class CheckbookGuru
{
  /**
   * @var string
   */
  private $solr_datasource;
  /**
   * @var CheckbookSolr
   */
  private $solr;
  /**
   * @var CheckbookSolrQuery
   */
  private $solr_query;

  /**
   * @var bool
   */
  private $parsed = FALSE;

  /**
   * @param string $solr_datasource
   * @return static::class
   */
  public static function get_instance(string $solr_datasource)
  {
    $cls = static::class;
    return new $cls($solr_datasource);
  }

  /**
   * CheckbookGuru constructor.
   * @param string $solr_datasource
   */
  public function __construct(string $solr_datasource)
  {
    $this->solr_datasource = $solr_datasource;
    $this->solr = new CheckbookSolr($solr_datasource);
    $this->solr_query = new CheckbookSolrQuery($solr_datasource);
  }

  /**
   * @param string $facet
   * @param array $filter
   * @return array
   */
  public function get_all(string $facet, array $filter = [])
  {
    $data = [];
    $this->solr_query
      ->addFacet($facet)
      ->setFacetLimit(500)
      ->setFacetSort(CheckbookSolrQuery::FACET_SORT_INDEX);
    $results = $this->solr
      ->request_phps('select/?' . $this->solr_query);

    if ($facets = ($results['facet_counts']['facet_fields'][$facet] ?? FALSE)) {
      $data = array_keys($facets);
      $data = $this->remove_duplicates($data);
    }

    return $data;
  }

  /**
   * @param $data
   * @return array
   */
  private function remove_duplicates($data)
  {
    $seen = [];
    $out = [];
    if (strpos($data[0], ']') && ']' == substr($data[0], -1)) {
      while ($data) {
        $facet = array_pop($data);
        $idx = strrpos($facet, '[');
        $id = substr($facet, $idx + 1, -1);
        if (in_array($id, $seen)) {
          continue;
        }
        $seen[] = $id;
        if ($this->parsed) {
          $out[] = [
            'id' => $id,
            'title' => trim(substr($facet, 0, $idx)),
            'value' => $facet
          ];
        } else {
          $out[] = $facet;
        }
      }

      return array_reverse($out);
    }
    return $data;
  }

  /**
   * @param string $facet
   * @param array $filter
   * @return array
   */
  public function get_all_parsed(string $facet, array $filter = [])
  {
    $this->parsed = TRUE;
    return $this->get_all($facet, $filter);
  }

  /**
   * @param string $key
   * @param string $facet_value_pair
   */
  public function setFq(string $key, string $facet_value_pair)
  {
    $this->solr_query->setFq($key, $facet_value_pair);
  }

  /**
   * @param string $facet
   * @param string $term
   */
  public function setFqAutocompleteTerm(string $facet, string $term)
  {
    $this->solr_query->setFqAutocompleteTerm($facet, $term);
  }
}
