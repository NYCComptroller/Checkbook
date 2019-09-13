<?php
/*
 * Redirects to the search results page upon submitting the search form
 *
*/

/**
 * Class CheckbookSolrQuery
 */
class CheckbookSolrQuery
{

  /**
   * @var array
   */
  private $facets = [];

  /**
   * @var string
   */
  private $q = '*:*';

  /**
   * @var array
   */
  private $fq = [];

  /**
   * @var array
   */
  private $tags = [];

  /**
   * @var array
   */
  private $intervals = [];

  /**
   * @var string
   */
  private $sort = '';

  /**
   * @var int
   */
  private $page = 0;

  /**
   * @var int
   */
  private $rows = 10;

  /**
   * @var string
   */
  private $wt = 'phps';

  /**
   * @var array
   */
  private $termFields = [];

  /**
   * @var string
   */
  private $termsRegex = '';

  /**
   * @var string
   */
  private $query = '';

  /**
   * @var array
   */
  private $selectedFacets = [];

  /**
   * @var array
   */
  private $paramMapping = [];
  /**
   * @var array
   */
  private $facetsConfig = [];
  /**
   * @var array
   */
  private $autocompleteMapping = [];
  /**
   * @var string
   */
  private $sortConfig = '';

  /**
   * @var int
   */
  private $facetLimit = 30;

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
  public function __construct($solr_datasource = '', $searchTerms = '', int $rows = 0, int $page = 0)
  {
    $this->paramMapping = (array)CheckbookSolr::getParamMapping();
    $this->facetsConfig = (array)CheckbookSolr::getFacetConfigByDatasource($solr_datasource);
    $this->sortConfig = CheckbookSolr::getSortConfig($solr_datasource);
    $this->autocompleteMapping = (array)CheckbookSolr::getAutocompleteMapping();

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
  public function setTermsRegex(string $termsRegex): CheckbookSolrQuery
  {
    $this->termsRegex = $termsRegex;
    return $this;
  }

  /**
   * @param string $sort
   * @return CheckbookSolrQuery
   */
  public function setSort(string $sort): CheckbookSolrQuery
  {
    $this->sort = $sort;
    return $this;
  }

  /**
   * @param string $index
   * @param string $fq
   * @return CheckbookSolrQuery
   */
  public function setFq(string $index, string $fq): CheckbookSolrQuery
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
   * @return string
   */
  public function getWt(): string
  {
    return $this->wt;
  }

  /**
   * @param int $page
   * @return CheckbookSolrQuery
   */
  public function setPage(int $page): CheckbookSolrQuery
  {
    $this->page = $page;
    return $this;
  }

  /**
   * @param int $num_rows
   * @return CheckbookSolrQuery
   */
  public function setRows(int $num_rows): CheckbookSolrQuery
  {
    $this->rows = $num_rows;
    return $this;
  }

  /**
   * @param string $wt
   * @return CheckbookSolrQuery
   */
  public function setWt(string $wt): CheckbookSolrQuery
  {
    $this->wt = $wt;
    return $this;
  }

  /**
   * @param array $termFieldArray
   * @return CheckbookSolrQuery
   */
  public function addTermFields(array $termFieldArray): CheckbookSolrQuery
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
  public function addTermField(string $termField): CheckbookSolrQuery
  {
    if (!in_array($termField, $this->termFields)) {
      $this->termFields[] = $termField;
    }
    return $this;
  }

  /**
   * @return string
   */
  public function buildQuery(): string
  {
    $this->query = '';

    $query = [];
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

      $query['terms.regex.flag'] = 'case_insensitive';
      $query['terms.regex'] = $this->termsRegex;

      $this->query = http_build_query($query) . $terms . '&wt=' . $this->wt . $csv_mv_separator;
      return $this->query;
    }

    $query = array_merge($query, [
      'start' => $this->page * $this->rows,
      'rows' => $this->rows,
    ]);

    $fq = '';
    if (sizeof($this->fq)) {
      foreach ($this->fq as $f => $v) {
        $tag = isset($this->tags[$f]) ? '{!tag=' . $this->tags[$f] . '}' : '';
        $fq .= '&fq=' . $tag . $v;
      }
    }

    $facets = '';
    $intervals = '';
    if (sizeof($this->facets) || sizeof($this->intervals)) {
      $facets = '&' . http_build_query([
          'facet' => 'true',
          'facet.mincount' => 1,
          'facet.sort' => 'count',
          'facet.limit' => $this->facetLimit
        ]);
      if ($this->facets) {
        foreach ($this->facets as $facet) {
          $tag = isset($this->tags[$facet]) ? '{!ex=' . $this->tags[$facet] . '}' : '';
          $facets .= '&' . http_build_query(['facet.field' => $tag . $facet]);
        }
      }
      if ($this->intervals) {
        foreach ($this->intervals as $facet => $intervalArray) {
          $tag = isset($this->tags[$facet]) ? '{!ex=' . $this->tags[$facet] . '}' : '';
          $intervals .= '&facet.interval=' . $tag . $facet;
          foreach ($intervalArray as $key => $interval) {
            $intervals .= "&f.{$facet}.facet.interval.set={!key={$key}}{$interval}";
          }
        }
      }
    }

//    http://sdw6.reisys.com:18983/solr/checkbook_nycha_dev.public.solr_nycha/select/?q=*:*&fq={!tag=tg0}annual_salary:[*%20TO%2025000]&facet=true&facet.mincount=1&facet.sort=count&facet.limit=30&facet.field=domain&facet.field=agreement_type_name&facet.field=payroll_type&facet.field=vendor_name&facet.field=responsibility_center_name&facet.field=funding_source_name&facet.field=grant_name&facet.field=fiscal_year&facet.field=facet_year_array&facet.field=contract_number&facet.field=release_number&facet.field=record_type&facet.field=display_industry_type_name&facet.field=civil_service_title&{!ex=tg0}facet.interval=annual_salary&f.annual_salary.facet.interval.set={!ex=tg0%20key=>200,000}(200000,*]&f.annual_salary.facet.interval.set={!ex=tg0%20key=150,000-200,000}(150000,200000]&f.annual_salary.facet.interval.set={!ex=tg0%20key=100,000-150,000}(100000,150000]&f.annual_salary.facet.interval.set={!ex=tg0%20key=75,000-100,000}(75000,100000]&f.annual_salary.facet.interval.set={!ex=tg0%20key=50,000-75,000}(50000,75000]&f.annual_salary.facet.interval.set={!ex=tg0%20key=25,000-50,000}(25000,50000]&f.annual_salary.facet.interval.set={!ex=tg0%20key=<25,000}[*,25000]&start=0&rows=0&sort=domain_ordering+asc%2Cdate_ordering+desc&wt=xml

    $sort = '';
    if ($this->sort) {
      $query['sort'] = $this->sort;
    }

    if ('*:*' == $this->q) {
      $q = 'q=*:*';
    } else {
      $q = 'q=text:' . $this->q . '*';
    }

    /**
     * ex.
     * http://sdw5.reisys.com:18983/solr/checkbook_dev.public.solr_index_full/select/?q=*:*&facet=true&facet.sort=count&facet.field=fiscal_year&wt=phps&facet.mincount=1
     */

    $this->query = $q . $fq . $sort . $facets . $intervals . '&' . http_build_query($query) . '&wt=' . $this->wt . $csv_mv_separator;

    return $this->query;
  }

  /**
   * @param string $string
   * @return string
   */
  public static function escape(string $string): string
  {
    /**
     * DO NOT CHANGE ORDER, BACKSLASH MUST BE FIRST
     */
    $escape_chars = explode(' ', '\\ + - & | ! ( ) { } [ ] ^ " ~ * ? : /');
    $replace_to = [];
    foreach ($escape_chars as $char) {
      $replace_to[] = "\\" . $char;
    }
    $return = str_replace($escape_chars, $replace_to, $string);
    $return = urlencode(strtolower($return));

    return $return;
  }

  /**
   * @param array $searchTerms
   * @return CheckbookSolrQuery
   */
  public function setSearchTerms(array $searchTerms): CheckbookSolrQuery
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
   * @param string $param
   */
  public function mapParam(string &$param): void
  {
    if (in_array($param, array_keys($this->paramMapping))) {
      $param = $this->paramMapping[$param];
    }
  }

  /**
   * @param string $term
   * @param bool $exclude
   * @return CheckbookSolrQuery
   */
  public function setFqTerm(string $term, bool $exclude = false): CheckbookSolrQuery
  {
    list($param, $value) = explode('=', urldecode($term));

    if ('all' == $value) {
      return $this;
    }

    $this->mapParam($param);
    $this->selectedFacets[$param] = explode('~', $value);

    if ('vendor_type' == $param) {
      $value = $this->get_vendor_type_mapping($value);
    } elseif ('contract_status' == $param) {
      $value = $this->get_contract_status_mapping($value);
    } elseif ('year' == $param) {
      $sub3 = substr($value, 0, 3);
      if ('fy~' == $sub3) {
        $param = 'fiscal_year_id';
        $value = str_replace($sub3, '', $value);
      } elseif ('cy~' == $sub3) {
        $param = 'calendar_fiscal_year_id';
        $value = str_replace($sub3, '', $value);
      }
    } elseif ('department_name' == $param) {
      $value = str_replace('__', '/', $value);
    }

    $values = explode('~', $value);

    $fq = [];
    foreach ($values as $value) {
      $minus = $exclude ? '-' : '';

      if (isset($this->facetsConfig[$param]->intervals)) {
        $value = $this->facetsConfig[$param]->intervals->$value;
        $value = str_replace(',', '%20TO%20', $value);
        $fq[] = $minus . $param . ':' . $value;
        continue;
      }

      $fq[] = $minus . $param . ':"' . self::escape($value) . '"';
    }

    if ($exclude) {
      $this->setFq($param, join('%20AND%20', $fq));
    } else {
      $this->setFq($param, join('%20OR%20', $fq));
    }

    return $this;
  }

  /**
   * @param string $facet
   * @param string $term
   * @return CheckbookSolrQuery
   */
  public function setFqAutocompleteTerm(string $facet, string $term): CheckbookSolrQuery
  {
    $term = htmlspecialchars_decode($term);
    $this->mapParam($facet);
    $autocompleteFacet = $this->autocompleteMapping[$facet] ?? $facet;

    unset($this->fq[$facet]);
    unset($this->fq[$autocompleteFacet]);

    $facetValue = '*';
    if ($term) {
      $terms = explode(' ', $term);
      $term = join('*%20AND%20', $terms);
      $facetValue = "($term*)";
    }
    $this->setFq($facet, $autocompleteFacet . ':' . $facetValue);

    $this->addFacet($facet);

    $this->facetLimit = 10;

    return $this;
  }

  /**
   * @param array $facets
   * @return CheckbookSolrQuery
   */
  public function addFacets(array $facets): CheckbookSolrQuery
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
  public function addFacet(string $facet): CheckbookSolrQuery
  {
    $this->mapParam($facet);

    if (!in_array($facet, $this->facets)) {
      $this->facets[] = $facet;
    }
    return $this;
  }

  /**
   * @param string $facet
   * @param array $intervals
   * @return CheckbookSolrQuery
   */
  public function addInterval(string $facet, array $intervals): CheckbookSolrQuery
  {
    $this->intervals[$facet] = $intervals;
    return $this;
  }

//  /**
//   * @return CheckbookSolrQuery
//   */
//  public function clearFacets(): CheckbookSolrQuery
//  {
//    $this->facets = [];
//    return $this;
//  }

//  /**
//   * @param string $facet
//   * @return CheckbookSolrQuery
//   */
//  public function excludeFqTermValues(string $facet): CheckbookSolrQuery
//  {
//    $this->mapParam($facet);
//
//    if (isset($this->fq[$facet])) {
//      $this->fq[$facet] = str_replace("$facet:", "-$facet:", $this->fq[$facet]);
//    }
//
//    return $this;
//  }

  /**
   * @param array $facets
   * @return CheckbookSolrQuery
   */
  public function tagFacets(array $facets): CheckbookSolrQuery
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
  public function getSelectedFacets(): array
  {
    return $this->selectedFacets;
  }

  /**
   * @param $reqParams
   * @return bool|string
   */
  private function get_vendor_type_mapping($reqParams)
  {
    $vendorTypeParam = explode('~', $reqParams);
    $values = [];
    foreach ($vendorTypeParam as $vendorType) {
      switch (strtolower(trim($vendorType))) {
        case 'pv':
          array_push($values, 'p');
          array_push($values, 'pm');
          break;
        case 'sv':
          array_push($values, 's');
          array_push($values, 'sm');
          break;
        case 'mv':
          array_push($values, 'sm');
          array_push($values, 'pm');
          break;
      }
    }
    return join('~', array_unique($values));
  }

  /**
   * @param $reqParams
   * @return string
   */
  private function get_contract_status_mapping($reqParams)
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
}
