<?php
/**
 * This file is part of the Checkbook NYC financial transparency software.
 *
 * Copyright (c) 2012 – 2023 New York City
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Drupal\checkbook_solr;

use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;
use Drupal\checkbook_log\LogHelper;

/**
 * Class CheckbookSolrQueryBase
 */
class CheckbookSolrQueryBase
{
  /**
   * @var array
   */
  protected $facets = [];

  /**
   * @var string
   */
  protected $q = '*:*';

  /**
   * @var array
   */
  protected $fq = [];

  /**
   * @var array
   */
  protected $tags = [];

  /**
   * @var array
   */
  protected $intervals = [];

  /**
   * @var string
   */
  protected $sort = '';

  /**
   * @var int
   */
  protected $page = 0;

  /**
   * @var int
   */
  protected $rows = 10;

  /**
   * @var string
   */
  protected $wt = 'phps';

  /**
   * @var array
   */
  protected $termFields = [];

  /**
   * @var string
   */
  protected $termsRegex = '';

  /**
   * @var string
   */
  protected $query = '';

  /**
   * @var array
   */
  protected $selectedFacets = [];

  /**
   * @var array
   */
  protected $paramMapping = [];
  /**
   * @var array
   */
  protected $facetsConfig = [];
  /**
   * @var array
   */
  protected $autocompleteMapping = [];
  /**
   * @var string
   */
  protected $sortConfig = '';

  /**
   * @var int
   */
  protected $facetLimit = 500;

  /**
   * @var string
   */
  protected $facetPrefix = '';

  /**
   * @var int
   */
  protected $autoSuggestionsLimit = 10;

  /**
   * @var string
   */
  protected $datasourcename = '';


  /**
   * @var string
   * count|index
   */
  protected $facetSort = '';

  /**
   * @return string
   */
  public function getWt()
  {
    return $this->wt;
  }

  /**
   * @param string $wt
   * @return CheckbookSolrQuery
   */
  public function setWt(string $wt)
  {
    $this->wt = $wt;
    return $this;
  }

  /**
   * @param string $param
   */
  public function mapParam(string &$param)
  {
    if (in_array($param, array_keys($this->paramMapping))) {
      $param = $this->paramMapping[$param];
    }
  }

  /**
   * @param array $facets
   * @return CheckbookSolrQuery
   */
  public function tagFacets(array $facets)
  {
    foreach (array_keys($facets) as $facet) {
      $tag = 'tg' . sizeof($this->tags);
      $this->tags[$facet] = $tag;
    }
    return $this;
  }

  /**
   * @return array
   */
  public function getSelectedFacets()
  {
    return $this->selectedFacets;
  }

  /**
   * @param string $facet
   * @param array $intervals
   * @return CheckbookSolrQuery
   */
  public function addInterval(string $facet, array $intervals)
  {
    $this->intervals[$facet] = $intervals;
    return $this;
  }

  /**
   * @return string
   */
  public function getPreFq(&$tag)
  {
    $pre_fq = '';
    if (sizeof($this->fq)) {
      foreach ($this->fq as $f => $v) {
        $tag = isset($this->tags[$f]) ? '{!tag=' . $this->tags[$f] . '}' : '';
        $pre_fq .= '&fq=' . $tag . $v;
      }
    }

    return $pre_fq;
  }

  /**
   * @return string
   */
  public function getPreFacets(&$tag) {
    $pre_facets = '';
    if (sizeof($this->facets)) {
      $pre_facets = '&' . http_build_query([
          'facet' => 'true',
          'facet.mincount' => 1,
          'facet.sort' => $this->facetSort,
          'facet.limit' => $this->facetLimit
        ]);

      foreach ($this->facets as $facet) {
        $tag = isset($this->tags[$facet]) ? '{!ex=' . $this->tags[$facet] . '}' : '';
        $pre_facets .= '&' . http_build_query(['facet.field' => $tag . $facet]);
      }

      if ($this->facetPrefix != ''){
        $pre_facets .= '&facet.prefix='.$this->facetPrefix;
      }
    }

    return $pre_facets;
  }

  /**
   * @return string
   */
  public function getPreIntervals(&$tag) {
    $pre_intervals = '';
    if (sizeof($this->facets)) {
      foreach ($this->intervals as $facet => $intervalArray) {
        $tag = isset($this->tags[$facet]) ? '{!ex=' . $this->tags[$facet] . '}' : '';
        $pre_intervals .= '&facet.interval=' . $tag . $facet;
        foreach ($intervalArray as $key => $interval) {
          $pre_intervals .= "&f.{$facet}.facet.interval.set={!key={$key}}{$interval}";
        }
      }
    }

    return $pre_intervals;
  }

  /**
   * @return string
   */
  public function getPreQ()
  {
    // Construct the q for solr query
    if ('*:*' == $this->q) {
      $pre_q = 'q=*:*';
    }
    else{
      if(preg_match('/^[a-zA-Z_]+:/', $this->q)){
        $pre_q= 'q='.$this->q;
      }
      else {
        $pre_q = 'q=text:' . $this->q;
      }
    }
    return $pre_q;
  }

  /**
   * @param $reqParams
   * @return bool|string
   */
  protected function get_vendor_type_mapping($reqParams)
  {
    $vendorTypeParam = explode('~', $reqParams);
    $values = [];
    foreach ($vendorTypeParam as $vendorType) {
      switch (strtolower(trim($vendorType))) {
        case 'pv':
          $values[] = 'p';
          $values[] = 'pm';
          break;
        case 'sv':
          $values[] = 's';
          $values[] = 'sm';
          break;
        case 'mv':
          $values[] = 'sm';
          $values[] = 'pm';
          break;
        default:
          break;
      }
    }
    return join('~', array_unique($values));
  }

  /**
   * @param $reqParams
   * @return string
   */
  protected function get_contract_status_mapping($reqParams)
  {
    switch (strtolower($reqParams)) {

      case 'p':
      case 'pending':
        return 'pending';
        break;

      case 'r':
      case 'a':
      case 'registered':
      case 'active':
      default:
        return 'registered';
        break;
    }
  }

  /**
   * @param $param,
   * @param $value,
   * @return string
   */
  protected function get_facet_prefix($param,$value)
  {
    // Add array fields which need facet.prefix to be set
    $prefix_fields = array("contract_entity_contract_number","contract_commodity_line","facet_year_array");
    if(in_array($param, $prefix_fields)){
      $fPrefix = $value;
    }
    return $fPrefix;
  }

  /**
   * @param $param,
   * @param $datasrouce,
   * @return id
   */
  protected function get_spending_category_mapping($exptype, $data_source = Datasource::CITYWIDE)
  {
    $spending_exptype = "'(^$exptype$)'";
    try {
      if ($data_source == Datasource::NYCHA) {
        $sql = "SELECT spending_category_id FROM ref_spending_category WHERE spending_category_code ~* " . $spending_exptype . " OR display_spending_category_name ~* " . $spending_exptype;
      }
      else{
        $sql = "SELECT spending_category_id FROM ref_spending_category WHERE spending_category_code ~* " . $spending_exptype . " OR spending_category_name ~* " . $spending_exptype;
      }
      $results = _checkbook_project_execute_sql_by_data_source($sql, $data_source);
      if ($results && $results[0]['spending_category_id']) {
        return $results[0]['spending_category_id'];
      }
    } catch (\Exception $e) {
      LogHelper::log_error("Error getting data from controller: \n" . $e->getMessage());
    }
    return null;
  }
}
