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
   * @return array
   */
  public function get_all(string $facet)
  {
    $data = [];
    $limit = ((object)$this->solr_query)->autoSuggestionLimit ? ((object)$this->solr_query)->autoSuggestionLimit : 500;
    $this->solr_query
      ->addFacet($facet)
      ->setFacetLimit($limit)
      ->setFacetSort(CheckbookSolrQuery::FACET_SORT_INDEX);
    $results = $this->solr
      ->request_phps('select/?' . $this->solr_query);

    if ($facets = ($results['facet_counts']['facet_fields'][$facet] ?? [])) {
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
   * @return array
   */
  public function get_all_parsed(string $facet)
  {
    $this->parsed = TRUE;
    return $this->get_all($facet);
  }

  public function get_options(string $domain, string $facet, array $filters=[])
  {
    $this->solr_query->setFq('domain',"domain:{$domain}");

    foreach($filters as $fq => $value){
      $value = CheckbookSolrQuery::escape(trim($value));
      if(!$value) {continue;}
      if ('term' == $fq) {
        if ($value) {
          $this->solr_query->setFqAutocompleteTerm($facet, $value);
        }
        continue;
      }
      if ($value) {
        $this->solr_query->setFq($fq,"{$fq}:{$value}");
      }
    }
    return $this->get_all($facet);
  }
}
