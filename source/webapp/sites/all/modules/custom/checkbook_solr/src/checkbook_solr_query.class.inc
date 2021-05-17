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
   *
   */
  const FACET_SORT_INDEX = 'index';
  /**
   *
   */
  const FACET_SORT_COUNT = 'count';
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
  private $facetLimit = 500;

  /**
   * @var string
   */
  private $facetPrefix = '';

  /**
   * @var int
   */
  private $autoSuggestionsLimit = 10;

  /**
   * @var string
   */
  private $datasourcename = '';


  /**
   * @var string
   * count|index
   */
  private $facetSort = self::FACET_SORT_COUNT;

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
    $this->datasourcename = CheckbookSolr::getDatasoruceName($solr_datasource);

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
   * @param string $autoCompleteFacet
   * @param string $autoCompleteText
   * @return CheckbookSolrQuery
   */
  public function setAutoQ(string $autoCompleteFacet ,string $autoCompleteText): CheckbookSolrQuery
  {
    if (isset($autoCompleteFacet)){
      $this->q = $autoCompleteFacet . ':' . $autoCompleteText;
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
   * @param string $facetSort
   * @return CheckbookSolrQuery
   */
  public function setFacetSort(string $facetSort): CheckbookSolrQuery
  {
    $this->facetSort = $facetSort;
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
   * @param int $limit
   * @return CheckbookSolrQuery
   */
  public function setFacetLimit(int $limit = 30): CheckbookSolrQuery
  {
    $this->facetLimit = $limit;
    return $this;
  }
  /**
   * @param int $prefixValue
   * @return CheckbookSolrQuery
   */
  public function setFacetPrefix(string $prefixValue): CheckbookSolrQuery
  {
    $this->facetPrefix = $prefixValue;
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
          'facet.sort' => $this->facetSort,
          'facet.limit' => $this->facetLimit
        ]);
      if ($this->facets) {
        foreach ($this->facets as $facet) {
          $tag = isset($this->tags[$facet]) ? '{!ex=' . $this->tags[$facet] . '}' : '';
          $facets .= '&' . http_build_query(['facet.field' => $tag . $facet]);
        }
      }

      if ($this->facetPrefix != ''){
        $facets .= '&facet.prefix='.$this->facetPrefix;
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

    // Construct the q for solr query
    if ('*:*' == $this->q) {
      $q = 'q=*:*';
    }
    else{
      if(preg_match('/^[a-zA-Z_]+:/', $this->q)){
        $q= 'q='.$this->q;
      }
      else {
        $q = 'q=text:' . $this->q;
      }
    }

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
    }
    elseif('spending_category' == $param){
      $value = $this->get_spending_category_mapping($value,$this->datasourcename);
      $param = 'spending_category_id';
    }
    elseif ('contract_status' == $param) {
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
    elseif('agreement_start_year' == $param ){
      if ($value != 'undefined'){
        $value = '(agreement_start_year:[*%20TO%20'. $value.']%20AND%20agreement_end_year:['.$value .'%20TO%20*])';
      }
      else{
        unset($this->selectedFacets['agreement_start_year']);
        $value = '';
      }
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
      elseif ($param == 'agreement_start_year'){
        $fq[] = $value;
        unset($this->selectedFacets['agreement_start_year']);
      }
      else {
        $fq[] = $minus . $param . ':"' . self::escape($value) . '"';
      }
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
    $term = htmlspecialchars_decode($term, ENT_QUOTES);
    $this->mapParam($facet);
    $autoCompleteFacet = $this->autocompleteMapping[$facet] ?? $facet;
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

  /**
   * @param $param,
   * @param $value,
   * @return string
   */
  private function get_facet_prefix($param,$value)
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
  private function get_spending_category_mapping($exptype, $data_source = Datasource::CITYWIDE)
  {
    try {
      $dataController = data_controller_get_instance();
      $results = $dataController->queryDataset($data_source.':spending_category', array('spending_category_id'), array('spending_category_code' => $exptype));
      if ($results && $results[0]['spending_category_id']) return $results[0]['spending_category_id'];
    } catch (Exception $e) {
      log_error("Error getting data from controller: \n" . $e->getMessage());
    }
    return null;
  }
}
