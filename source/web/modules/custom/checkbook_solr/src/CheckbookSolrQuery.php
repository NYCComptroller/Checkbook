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

require_once(\Drupal::service('extension.list.module')->getPath('checkbook_smart_search') . "/includes/checkbook_autocomplete_functions.inc");

/**
 * Class CheckbookSolrQuery
 */
class CheckbookSolrQuery extends CheckbookSolrQueryBase
{
  /**
   *
   */
  const FACET_SORT_INDEX = 'index';

  /**
   *
   */
  const FACET_SORT_COUNT = 'count';

  /**
   * @var string
   * count|index
   */
  protected $facetSort = self::FACET_SORT_COUNT;

  /**
   * @return string
   */
  public function __toString()
  {
    return $this->buildQuery();
  }

  /**
   * CheckbookSolrQuery constructor.
   * @param string $solr_datasource
   * @param string $searchTerms
   * @param int $rows
   * @param int $page
   */
  public function __construct(string $solr_datasource = '', $searchTerms = '', int $rows = 0, int $page = 0)
  {
    $this->paramMapping = (array)CheckbookSolr::getParamMapping();
    $this->facetsConfig = (array)CheckbookSolr::getFacetConfigByDatasource($solr_datasource);
    $this->sortConfig = CheckbookSolr::getSortConfig($solr_datasource);
    $this->autocompleteMapping = (array)CheckbookSolr::getAutocompleteMapping();
    $this->datasourcename = $solr_datasource;

    if ($searchTerms) {
      $this
        ->setSearchTerms(explode('*!*', $searchTerms));
    }

    $this
      ->setSort($this->sortConfig);

    $this
      ->setRows($rows)
      ->setPage($page);
    return $this;
  }

  /**
   * @param string $termsRegex
   * @return CheckbookSolrQuery
   */
  public function setTermsRegex(string $termsRegex)
  {
    $this->termsRegex = $termsRegex;
    return $this;
  }

  /**
   * @param string $sort
   * @return CheckbookSolrQuery
   */
  public function setSort(string $sort)
  {
    $this->sort = $sort;
    return $this;
  }

  /**
   * @param string $index
   * @param string $fq
   * @return CheckbookSolrQuery
   */
  public function setFq(string $index, string $fq)
  {
    $this->mapParam($index);
    if (!$fq && isset($this->fq[$index])) {
      unset($this->fq[$index]);
    } else {
      $this->fq[$index] = $fq;
    }
    return $this;
  }

  /**
   * @param string $autoCompleteFacet
   * @param string $autoCompleteText
   * @return CheckbookSolrQuery
   */
  public function setAutoQ(string $autoCompleteFacet ,string $autoCompleteText)
  {
    if (isset($autoCompleteFacet)){
      $this->q = $autoCompleteFacet . ':' . $autoCompleteText;
    }
    return $this;
  }

  /**
   * @param int $page
   * @return CheckbookSolrQuery
   */
  public function setPage(int $page)
  {
    $this->page = $page;
    return $this;
  }

  /**
   * @param string $facetSort
   * @return CheckbookSolrQuery
   */
  public function setFacetSort(string $facetSort)
  {
    $this->facetSort = $facetSort;
    return $this;
  }

  /**
   * @param int $num_rows
   * @return CheckbookSolrQuery
   */
  public function setRows(int $num_rows)
  {
    $this->rows = $num_rows;
    return $this;
  }

  /**
   * @param int $limit
   * @return CheckbookSolrQuery
   */
  public function setFacetLimit(int $limit = 30)
  {
    $this->facetLimit = $limit;
    return $this;
  }
  /**
   * @param int $prefixValue
   * @return CheckbookSolrQuery
   */
  public function setFacetPrefix(string $prefixValue)
  {
    $this->facetPrefix = $prefixValue;
    return $this;
  }

  /**
   * @param array $termFieldArray
   * @return CheckbookSolrQuery
   */
  public function addTermFields(array $termFieldArray)
  {
    foreach ($termFieldArray as $term) {
      $this->addTermField($term);
    }
    return $this;
  }

  /**
   * @param string $termField
   * @return CheckbookSolrQuery
   */
  public function addTermField(string $termField)
  {
    if (!in_array($termField, $this->termFields)) {
      $this->termFields[] = $termField;
    }
    return $this;
  }

  /**
   * @return string
   */
  public function buildQuery()
  {
    $this->query = '';

    $pre_query = [];
    /**
     * NYCCHKBK-9184
     * somehow multivalued CSV export keeps over-escaping commas after csv joining values
     * and there is no way to put csv.mv.separator to NONE so | works somehow
     */
    $csv_mv_separator = ('csv' == $this->wt) ? '&csv.mv.separator=|' : '';

    if (sizeof($this->termFields)) {
      $terms = '';
      foreach ($this->termFields as $termField) {
        $terms .= '&' . http_build_query(['terms.fl' => $termField]);
      }

      $pre_query['terms.regex.flag'] = 'case_insensitive';
      $pre_query['terms.regex'] = $this->termsRegex;

      $this->query = http_build_query($pre_query) . $terms . '&wt=' . $this->wt . $csv_mv_separator;
      return $this->query;
    }

    $pre_query = array_merge($pre_query, [
      'start' => $this->page * $this->rows,
      'rows' => $this->rows,
    ]);

    $pre_fq = $this->getPreFq($tag);
    $pre_facets = $this->getPreFacets($tag);
    $pre_intervals = $this->getPreIntervals($tag);

//    http://sdw6.reisys.com:18983/solr/checkbook_nycha_dev.public.solr_nycha/select/?q=*:*&fq={!tag=tg0}annual_salary:[*%20TO%2025000]&facet=true&facet.mincount=1&facet.sort=count&facet.limit=30&facet.field=domain&facet.field=agreement_type_name&facet.field=payroll_type&facet.field=vendor_name&facet.field=responsibility_center_name&facet.field=funding_source_name&facet.field=grant_name&facet.field=fiscal_year&facet.field=facet_year_array&facet.field=contract_number&facet.field=release_number&facet.field=record_type&facet.field=display_industry_type_name&facet.field=civil_service_title&{!ex=tg0}facet.interval=annual_salary&f.annual_salary.facet.interval.set={!ex=tg0%20key=>200,000}(200000,*]&f.annual_salary.facet.interval.set={!ex=tg0%20key=150,000-200,000}(150000,200000]&f.annual_salary.facet.interval.set={!ex=tg0%20key=100,000-150,000}(100000,150000]&f.annual_salary.facet.interval.set={!ex=tg0%20key=75,000-100,000}(75000,100000]&f.annual_salary.facet.interval.set={!ex=tg0%20key=50,000-75,000}(50000,75000]&f.annual_salary.facet.interval.set={!ex=tg0%20key=25,000-50,000}(25000,50000]&f.annual_salary.facet.interval.set={!ex=tg0%20key=<25,000}[*,25000]&start=0&rows=0&sort=domain_ordering+asc%2Cdate_ordering+desc&wt=xml

    $pre_sort = '';
    if ($this->sort) {
      $pre_query['sort'] = $this->sort;
    }

    $pre_q = $this->getPreQ();

    /**
     * ex. old
     * http://sdw5.reisys.com:18983/solr/checkbook_dev.public.solr_index_full/select/?q=*:*&facet=true&facet.sort=count&facet.field=fiscal_year&wt=phps&facet.mincount=1
     * ex. new advanced search autocomplete
     * http://sdw6.reisys.com:18986/solr/checkbook_dev.public.solr_citywide/select/?q=vendor_name_autocomplete:(tobay*)&fq=contract_status:"pending"&facet=true&facet.mincount=1&facet.sort=count&facet.limit=10&facet.field=vendor_name&start=0&rows=0&sort=domain_ordering+asc%2C+date_ordering+desc%2C+check_eft_issued_date+desc%2C+revenue_budget_fiscal_year+desc%2C+current_budget_amount+desc%2C+id+desc&wt=php
     * ex. smart search facet autocomplete
     * http://sdw4.reisys.com:18985/solr/checkbook_nycha_dev.public.solr_nycha/select/?q=funding_source_name_autocomplete:(lihtc*%20AND%20%5C-*%20AND%20castle*%20AND%20hill*)&fq=domain:"revenue"&facet=true&facet.mincount=1&facet.sort=count&facet.limit=10&facet.field=funding_source_name&start=0&rows=0&sort=domain_ordering+asc%2C+sort_sequence+asc%2C+check_issue_date+desc%2C+document_id+desc%2C+date_ordering+desc%2C+invoice_number+asc%2C+invoice_line_number+asc%2C+distribution_line_number+asc%2C+id+desc&wt=php
     * ex. query after unsetting sort for autocompletes
     * http://sdw4.reisys.com:18983/solr/checkbook_nycha_dev.public.solr_nycha/select/?q=facet_year_array:(2011*)&facet=true&facet.mincount=1&facet.sort=count&facet.limit=10&facet.field=facet_year_array&facet.prefix=2011&start=0&rows=0&wt=php
     */

    $this->query = $pre_q . $pre_fq . $pre_sort . $pre_facets . $pre_intervals . '&' . http_build_query($pre_query) . '&wt=' . $this->wt . $csv_mv_separator;

    return $this->query;
  }

  /**
   * @param string $string
   * @return string
   */
  public static function escape(string $string)
  {
    /**
     * DO NOT CHANGE ORDER, BACKSLASH MUST BE FIRST
     */
    $escape_chars = ['\\', '.', '^', '$', '*', '+', '(', ')', '[', ']', '{', '}', '-', '&', '', '|', '!', '"', '?', ':', '/'];
    $replace_to = [];
    $setFlag = 0;

    // NYCCHKBK-10081 "\\" in for partial search text citywide is not generating results because \\ get converted '\\\\'
    // When * is present in the beginning or ending of string do not add \\
    if ((preg_match("/^\*/", $string)) || (preg_match("/\*$/", $string))){
      $newString = substr($string,1);
      // if string has is special characters need to add "\\" in front to escape characters to get the results
      if (preg_match_all("/['^.$%&()}{\[\]\"#~?><>,|=!:\/_+-]/i",$newString,$matches)){
        $setFlag = 0;
      }
      else{
        $setFlag = 1;
      }
    }
    foreach ($escape_chars as $char) {
      if ($setFlag == 1){
          $replace_to[] = $char;
      }
      else{
        $replace_to[] = "\\" . $char;
      }
    }
    $return = str_replace($escape_chars, $replace_to, $string);
    $return = urlencode(strtolower($return));

    return $return;
  }

  /**
   * @param array $searchTerms
   * @return CheckbookSolrQuery
   */
  public function setSearchTerms(array $searchTerms)
  {
    $searchTerms[0] = urldecode($searchTerms[0]); //"Education"
    $searchTerms[0] = ($searchTerms[0] == "") ? "*:*" : strtolower(CheckbookSolrQuery::escape($searchTerms[0]));

    $this->q = array_shift($searchTerms);

    if (!sizeof($searchTerms)) {
      return $this;
    }

    foreach ($searchTerms as $term) {
      $this->setFqTerm($term);
    }

    return $this;
  }

  /**
   * @param string $term
   * @param bool $exclude
   * @return CheckbookSolrQuery
   */
  public function setFqTerm(string $term, bool $exclude = false)
  {
    list($param, $value) = explode('=', urldecode($term));
    if ('all' == $value) {
      return $this;
    }

    $this->mapParam($param);
    $this->selectedFacets[$param] = explode('~', $value);

    switch ($param) {
      case 'vendor_type':
        $value = $this->get_vendor_type_mapping($value);
        break;

      case 'spending_category_code':
        $value = $this->get_spending_category_mapping($value,$this->datasourcename);
        $param = 'spending_category_id';
        break;

      case 'contract_status':
        $value = $this->get_contract_status_mapping($value);
        break;

      case 'year':
        $sub3 = substr($value, 0, 3);
        switch ($sub3) {
          case 'fy~':
            $param = 'fiscal_year_id';
            $value = str_replace($sub3, '', $value);
            break;
          case 'cy~':
            $param = 'calendar_fiscal_year_id';
            $value = str_replace($sub3, '', $value);
            break;
          default:
            break;
        }
        break;

      case 'department_name':
        $value = str_replace('__', '/', $value);
        break;

      case 'agreement_start_year':
        if ($value != 'undefined'){
          $value = '(agreement_start_year:[*%20TO%20'. $value.']%20AND%20agreement_end_year:['.$value .'%20TO%20*])';
        }
        else{
          unset($this->selectedFacets['agreement_start_year']);
          $value = '';
        }
        break;

      default:
        break;
    }

    $values = explode('~', $value);

    $fq = [];
    foreach ($values as $value) {
      $minus = $exclude ? '-' : '';
      if (isset($this->facetsConfig[$param]->intervals)) {
        $value = $this->facetsConfig[$param]->intervals->$value;
        $value = str_replace(',', '%20TO%20', $value);
        $fq[] = $minus . $param . ':' . $value;
      } elseif ($param == 'agreement_start_year'){
        $fq[] = $value;
        unset($this->selectedFacets['agreement_start_year']);
      }
      //Adjust vendor count for contracts when subvendor is present for a prime vendor
      elseif ($param == 'vendor_name' && $this->datasourcename == Datasource::SOLR_CITYWIDE){
        $fq[] = $minus . $param . ':"' . self::escape($value) . '"OR%20contract_prime_vendor_name:"'.self::escape($value) . '"';
      }else if($param == 'event_id'){
        $fq[] = $minus . $param . ':*"' . self::escape($value) . '"*';
      } else {
        $fq[] = $minus . $param . ':"' . self::escape($value) . '"';
      }
    }

    $this->setFq($param, join('%20' . ($exclude ? 'AND' : 'OR') . '%20', $fq));
    return $this;
  }

  /**
   * @param string $facet
   * @param string $term
   * @return CheckbookSolrQuery
   */
  public function setFqAutocompleteTerm(string $facet, string $term)
  {
    $term = htmlspecialchars_decode($term, ENT_QUOTES);
    $this->mapParam($facet);
    $autoCompleteFacet = ($this->autocompleteMapping[$facet]) ? $this->autocompleteMapping[$facet] : $facet;
    $facetPrefixValue = $this->get_facet_prefix($facet,$term);
    if (isset($facetPrefixValue)){$this->setFacetPrefix($facetPrefixValue);}
    unset($this->fq[$facet]);
    unset($this->fq[$autoCompleteFacet]);
    $autoCompleteText = _get_autocomplete_search_term($term);

    // Set the value of q based on the autocomplete field
    $this->setAutoQ($autoCompleteFacet ,$autoCompleteText);
    $this->addFacet($facet);
    $this->setFacetLimit((int)$this->autoSuggestionsLimit);
    unset($this->sort);
    return $this;
  }

  /**
   * @param array $facets
   * @return CheckbookSolrQuery
   */
  public function addFacets(array $facets)
  {
    foreach ($facets as $facet) {
      $this->addFacet($facet);
    }
    return $this;
  }

  /**
   * @param string $facet
   * @return CheckbookSolrQuery
   */
  public function addFacet(string $facet)
  {
    $this->mapParam($facet);

    if (!in_array($facet, $this->facets)) {
      $this->facets[] = $facet;
    }
    return $this;
  }
}
