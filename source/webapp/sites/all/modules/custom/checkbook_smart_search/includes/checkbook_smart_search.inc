<?php
/*
 * Redirects to the search results page upon submitting the search form
 *
*/

/** Submit handler for smart serch
 * @param $form
 * @param $form_state
 */
function _checkbook_smart_search_submit($form, &$form_state)
{
  $solr_datasource = _checkbook_get_datasource();
  $search_term = trim($form['search_box']['#value']);
  if ($search_term == 'Type ahead search') {
    $search_term = '';
  }
  $search_term = ($search_term) ? $search_term : '';

  $form_state['redirect'] = array(
    'smart_search/' . $solr_datasource,
    array(
      'query' => array(
        'search_term' => urlencode($search_term)
      )
    )
  );
}

/**
 * @param string $solr_datasource
 * @param CheckbookSolrQuery $solr_query
 * @param string $facet
 * @return array
 */
function _checkbook_smart_search_get_unchecked_facets(string $solr_datasource, CheckbookSolrQuery $solr_query, string $facet): array
{
  $solr = CheckbookSolr::getInstance($solr_datasource);

  $facet_query = clone $solr_query;
  $facet_query
    ->setRows(0)
    ->clearFacets()
    ->addFacet($facet)
    ->excludeFqTermValues($facet);
  $facet_results = $solr->request_phps('select/?'.$facet_query->buildQuery());

  return $facet_results['facet_counts']['facet_fields'][$facet];
}

/** Returns results for smart search
 * @param string $solr_datasource
 * @return mixed|string
 * @throws Exception
 */
function _checkbook_smart_search_get_results(string $solr_datasource = 'citywide')
{
  $search_terms = explode('*|*', $_REQUEST['search_term']); // array("Education", "domains=Payroll~Spending", "fiscal_years=2014~2013")

  $solr_query = new CheckbookSolrQuery();
  $solr_query
    ->setSearchTerms($search_terms);

  //Search results
  $page_no = ($_REQUEST['page']) ? $_REQUEST['page'] : 0; // 0
  $results_per_page = 10;

  $solr_query
    ->setSort('domain_ordering asc,date_ordering desc')
    ->setRows($results_per_page)
    ->setPage($page_no);

  $datasource_facets = CheckbookSolr::getFacetConfigByDatasource($solr_datasource);

  $selected_facets = $solr_query->getSelectedFacets();

  foreach($datasource_facets as $facet_name => $facet) {
    if (!$facet->no_autoload) {
      $solr_query->addFacet($facet_name);
    }
    if ($facet->sub && in_array($facet_name, array_keys($selected_facets))) {
      foreach($selected_facets[$facet_name] as $facet_value) {
        if ($domain_facets = $facet->sub->$facet_value ?? false) {
          $solr_query->addFacets($domain_facets);
        }
      }
    }
  }

  $reg_flag = false;
//  $act_flag = false;
  foreach ($search_terms as $id => $keyvaluepair) {
    $keys = explode("=", $keyvaluepair);
    if ($keys[1] == 'registered') {
      $reg_flag = true;
    } else {
//      $act_flag = true;
    }
  }

  if ($reg_flag && in_array('registered_fiscal_years', array_keys($datasource_facets))) {
    $solr_query->addFacet('registered_fiscal_years');
  } else {
    $solr_query->addFacet('fiscal_years');
  }

  $selected_facets = $solr_query->getSelectedFacets();

  $solr = new CheckbookSolr($solr_datasource);
  $search_results = $solr->request_phps('select/?'.$solr_query->buildQuery());

  $vendor_number = count($search_results['facet_counts']['facet_fields']['vendor_name']);
  $vendor_pages = floor($vendor_number / 100);
  drupal_add_js(array('checkbook_smart_search' => array('vendor_pages' => $vendor_pages)), 'setting');

  $expense_categories_number = 0;
  if ($search_results['facet_counts']['facet_fields']['expenditure_object_name'] ?? false) {
    $expense_categories_number = sizeof($search_results['facet_counts']['facet_fields']['expenditure_object_name']);
  }

  $expense_category_pages = floor($expense_categories_number / 100);
  drupal_add_js(array('checkbook_smart_search' => array('expense_category_pages' => $expense_category_pages)), 'setting');

  if ($selected_facets) {
    foreach(array_keys($selected_facets) as $facet) {
      $unchecked_facet_fields = _checkbook_smart_search_get_unchecked_facets($solr_datasource, $solr_query, $facet);
      if ($unchecked_facet_fields) {
        $search_results['facet_counts']['facet_fields'][$facet] += $unchecked_facet_fields;
      }
    }
  }

  drupal_add_js(array('checkbook_smart_search' => array('solr_datasource' => $solr_datasource)), 'setting');

  return theme('checkbook_smart_search_results', [
    'solr_datasource' => $solr_datasource,
    'search_results' => $search_results,
//    'search_results' => $search_results['search_results'],
    'active_contracts' => $search_results['active_contracts'],
    'registered_contracts' => $search_results['registered_contracts'],
    'facet_results' => $search_results['facet_counts']['facet_fields'],
    'selected_facet_results' => $selected_facets,
//    'selected_facet_results' => $selected_facet_results,
  ]);
}

/**
 * /smart_search/autocomplete_facet/nycha/vendor_name?term=RICHARDS
 *
 * @param string $solr_datasource
 * @param string $facet_name
 */
function _checkbook_smart_search_facet_autocomplete(string $solr_datasource, string $facet_name)
{
  $search_terms = explode('*|*', $_REQUEST['search_term']); // array("Education", "domains=Payroll~Spending", "fiscal_years=2014~2013")

  $solr_query = new CheckbookSolrQuery();
  $solr_query
    ->setSearchTerms($search_terms)
    ->setRows(0);

  $term = trim(htmlspecialchars_decode($_REQUEST['term'], ENT_QUOTES));//r*ei s.y|:s
  if ($term) {
    $terms = array_map('CheckbookSolrQuery::escape', explode(' ', $term)); // [r*ei, s.y|s]
    $fq_term = $facet_name . ':(' . join('"*%20AND%20"', $terms) . '*)';
    $solr_query
      ->removeFqTermValues($facet_name)
      ->addFq($fq_term);
  }

  $solr_query->addFacet($facet_name);

  $mapped_facet = $facet_name;
  $solr_query->mapParam($mapped_facet);

  $solr = CheckbookSolr::getInstance($solr_datasource);
  $results = $solr->request_phps('select/?'.$solr_query->buildQuery());

  if ($results['response']['numFound'] != 0) {
    $facet_results = $results['facet_counts']['facet_fields'][$mapped_facet];
    $matches = array();
    foreach ($facet_results as $key => $value) {
      if ($value > 0) {
        $key = (string)$key;
        array_push($matches, array("url" => "", "category" => $facet_name, "label" => $key, "value" => urlencode($key)));
      }
    }

    return drupal_json_output($matches);
  }
  return drupal_json_output([['label' => 'No Matches Found', 'value' => 'No Matches Found']]);
}


/**
 * Outputs search results using the template file 'checkbook_smart_search_results.tpl.php'.
 * @param $search_terms
 * @param $page_no
 * @param $no_of_results
 * @param string $domain
 * @return array
 */
function _checkbook_smart_search_results($search_terms, $page_no, $no_of_results, string $domain = 'citywide')
{
  $search_string = _checkbook_smart_search_get_results_query_search_term($search_terms);
  $query = "select/?q=" . $search_string
    . "&start=" . ($page_no * $no_of_results)
    . "&sort=domain_ordering+asc,date_ordering+desc"
    . "&wt=phps";

  $search_results = _checkbook_smart_search_solr_results($query, $domain);

  $contract_flag = false;
  foreach ($search_terms as $id => $keyvaluepair) {
    $keys = explode("=", $keyvaluepair);
    if ($keys[0] == 'domains') {
      $tilda = strpos($keys[1], '~');
      if ($tilda) {
        $domain = explode('~', $keys[1]);
        foreach ($domain as $key => $value) {
          if ($value == 'contracts') {
            $contract_flag = true;
          }
        }
      } else {
        if ($keys[1] == 'contracts') {
          $contract_flag = true;
        }
      }
    }
  }

  if ($contract_flag) {
    //Active contracts count
    $act_search_string = str_replace('&fq=(contract_status:"pending")', '', $search_string);
    foreach ($search_terms as $id => $keyvaluepair) {
      $keys = explode("=", $keyvaluepair);
      if ($keys[0] == 'registered_fiscal_years') {
        $act_search_string = str_replace('registered_fiscal_year', 'fiscal_year', $act_search_string);
      }
    }
    $active_contracts_query = "select/?q=" . $act_search_string
      . "&fq=contract_status:registered"
      . "&wt=phps";

    $active_contracts = _checkbook_smart_search_solr_results($active_contracts_query, $domain);

    // Registered Contracts count
    $reg_search_string = str_replace('&fq=(contract_status:"pending")', '', $search_string);
    foreach ($search_terms as $id => $keyvaluepair) {
      $keys = explode("=", $keyvaluepair);
      if ($keys[0] == 'fiscal_years') {
        $reg_search_string = str_replace('fiscal_year', 'registered_fiscal_year', $reg_search_string);
      }
    }
    $registered_contracts_query = "select/?q=" . $reg_search_string
      . "&fq=contract_status:registered"
      . "&wt=phps";

    $registered_contracts = _checkbook_smart_search_solr_results($registered_contracts_query, $domain);
  }

  return array('search_results' => $search_results, 'active_contracts' => $active_contracts, 'registered_contracts' => $registered_contracts);
}

/** Returns solr results based on the search criteria from current URL
 * @param $search_terms
 * @return string
 */
function _checkbook_smart_search_get_results_query_search_term($search_terms)
{
  $parameterMapping = array(
    "oge_agency_names" => "oge_agency_name",
    "agency_names" => "agency_name",
    "fiscal_years" => "fiscal_year",
    "registered_fiscal_years" => "registered_fiscal_year",
    "domains" => "domain",
    "vendor_names" => "vendor_name",
    "vendor_type" => "vendor_type",
    "expense_categories" => "expenditure_object_name",
    "revenue_categories" => "revenue_category_name",
    "spending_categories" => "spending_category_name",
    "contract_categories" => "contract_category_name",
    "contract_status" => "contract_status",
    "minority_type_name" => "minority_type_name",
    "industry_type_name" => "industry_type_name",
    "payroll_type" => "payroll_type"
  );

  $searchTerm = $search_terms[0];

  for ($i = 1; $i < count($search_terms); $i++) {
    $reqParams = explode('=', $search_terms[$i]);
    if ($reqParams[0] == 'vendor_type') {
      $reqParams[1] =  get_vendor_type_mapping($reqParams[1]);
    }
    if ($reqParams[0] == 'contract_status') {
      $reqParams[1] = get_contract_status_mapping($reqParams[1]);
    }
    $filters = explode('~', urldecode($reqParams[1]));

    $subQuery = "";
    foreach ($filters as $value) {
      $subQuery .= "(" . $parameterMapping[$reqParams[0]] . ':"' . CheckbookSolrQuery::escape($value) . '")%20OR%20';
    }
    $subQuery = substr($subQuery, 0, -8);
    $searchTerm .= "&fq=" . $subQuery;
  }

  return $searchTerm;
}

/**
 *  Outputs the solr query result for the input query.
 * @param string $query
 *  Returns the solr result in PHP array format
 * @param string $datasource
 * @return array
 */
function _checkbook_smart_search_solr_results(string $query, string $datasource = 'citywide'): array
{
  if ('citywide' == $datasource) {
    $datasource = _checkbook_get_datasource();
  }
  $solr = new CheckbookSolr($datasource);
  return $solr->request_phps($query);
}

/**
 *  Outputs the suggestions for the given input.
 *  Returns the matching words
 *
 * @param string $domain
 */
function _checkbook_smart_search_autocomplete_main_input($domain = 'citywide')
{
  $search_term = htmlspecialchars_decode($_REQUEST['term'], ENT_QUOTES);
  $search_term = strtolower($search_term);

  $search_term = '(.* ' . $search_term . '.*)|(^' . $search_term . '.*)';

  $query = new CheckbookSolrQuery();
  $query
    ->addTermField('agency_name')
    ->addTermField('oge_agency_name')
    ->addTermField('vendor_name')
    ->addTermField('minority_type_name')
    ->addTermField('revenue_category_name')
    ->addTermField('expenditure_object_name')
    ->setTermsRegex($search_term);

  $solr = new CheckbookSolr($domain);
  $contents = $solr->requestTerms($query);


  $smart_search_url = '/smart_search/' . $domain;

  $matches = array();
  $count = 0;
  foreach ($contents['terms'] as $key => $value) {
    switch ($key) {
      case 'agency_name':
        foreach ($value as $a => $b) {
          if (!in_array($a, $contents['terms']['oge_agency_name'])) {
            $count++;
            $url = $smart_search_url . "?search_term=*|*agency_names=";
            $matches[] = array("url" => $url, "category" => "Agencies", "label" => $a, "value" => urlencode($a));
          }
        }
        break;
      case 'oge_agency_name':
        foreach ($value as $a => $b) {
          $count++;
          $url = $smart_search_url . "?search_term=*|*oge_agency_names=";
          $matches[] = array("url" => $url, "category" => "Other Government Entities", "label" => $a, "value" => urlencode($a));
        }
        break;
      case 'vendor_name':
        foreach ($value as $a => $b) {
          $count++;
          $url = $smart_search_url . "?search_term=*|*vendor_names=";
          array_push($matches, array("url" => $url, "category" => "Vendors (Prime Vendor, Sub Vendor, M/WBE Vendor)", "label" => $a, "value" => urlencode($a)));
        }
        break;
      case 'minority_type_name':
        foreach ($value as $a => $b) {
          $count++;
          $url = $smart_search_url . "?search_term=*|*minority_type_name=";
          array_push($matches, array("url" => $url, "category" => "M/WBE Category", "label" => $a, "value" => urlencode($a)));
        }
        break;
      case 'expenditure_object_name':
        foreach ($value as $a => $b) {
          $count++;
          $url = $smart_search_url . "?search_term=*|*expense_categories=";
          array_push($matches, array("url" => $url, "category" => "Expense Categories", "label" => $a, "value" => urlencode($a)));
        }
        break;
    }

  }
  if ($count == 0) {
    array_push($matches, array("url" => "", "label" => '<span>' . "No matches found" . '</span>', 'value' => 'No matches found'));
  }

  return drupal_json_output($matches);
}


/**
 *  Outputs query string for facet auto complete fields
 *  Return the Query String
 * @param $facet_name
 * @param $facet_autocomplete_field
 */
function _checkbook_smart_search_get_searchstring_autocomplete($facet_name, $facet_autocomplete_field)
{
//"vendor_names","vendor_name_autocomplete"
// "oge_agency_names","oge_agency_name_autocomplete"
  $parameter_mapping = array(
    "oge_agency_names" => "oge_agency_name",
    "agency_names" => "agency_name",
    "fiscal_years" => "fiscal_year",
    "registered_fiscal_years" => "registered_fiscal_year",
    "domains" => "domain",
    "vendor_type" => "vendor_type",
    "vendor_names" => "vendor_name",
    "expense_categories" => "expenditure_object_name",
    "revenue_categories" => "revenue_category_name",
    "spending_categories" => "spending_category_name",
    "contract_categories" => "contract_category_name",
    "contract_status" => "contract_status",
    "minority_type_name" => "minority_type_name",
    "industry_type_name" => "industry_type_name",
    "payroll_type" => "payroll_type"
  );

  $autocomplete_text = trim(htmlspecialchars_decode($_REQUEST['term'], ENT_QUOTES));//eco
  $autocomplete_terms = explode(' ', $autocomplete_text); // (eco)

  if (count($autocomplete_terms) > 0) {
    $autocomplete_text = "";
    foreach ($autocomplete_terms as $key => $value) {
      if (strlen($value) > 0)
        $autocomplete_text .= CheckbookSolrQuery::escape($value) . "*%20AND%20"; //eco*%20AND%20
    }
    $autocomplete_text = "(" . substr($autocomplete_text, 0, -9) . ")"; //(eco*)
  } else {
    $autocomplete_text = CheckbookSolrQuery::escape($autocomplete_text) . "*";
  }

  $autocomplete_text = "&fq=" . $facet_autocomplete_field . ":" . $autocomplete_text; // &fq=oge_agency_name_autocomplete:(eco*)

  $request_search_term = $_REQUEST['search_term']; // ""
  $search_terms = explode('*|*', $request_search_term);// ()
  if ($search_terms[0] == "") {
    $search_text = "*:*"; //*:*
  } else {
    $search_text = CheckbookSolrQuery::escape(urldecode($search_terms[0]));
    $search_text = ucwords(strtolower($search_text));
  }

  $search_string = $search_text; //*:*
  for ($i = 1; $i < count($search_terms); $i++) {
    $req_params = explode('=', $search_terms[$i]);
    if ($req_params[0] == 'vendor_type') {
      $req_params[1] = get_vendor_type_mapping($req_params[1]);
    }
    if ($req_params[0] == 'contract_status') {
      if ($req_params[1] == 'active') {
        $req_params[1] = 'registered';
      }
    }

    $filters = explode('~', $value = urldecode($req_params[1]));
    $sub_query = "";
    foreach ($filters as $key => $value) {
      if ($req_params[0] != $facet_name) {
        $sub_query .= "(" . $parameter_mapping[$req_params[0]] . ":" .
          CheckbookSolrQuery::escape('"' . $value . '"') . ")%20OR%20";
      }
    }
    $search_string .= (strlen($sub_query) > 0) ? "&fq=" . substr($sub_query, 0, -8) : "";
  }
  $query = "select/?q=" . $search_string // *:*
    . $autocomplete_text //&fq=oge_agency_name_autocomplete:(eco*)
    . "&facet=true"
    . "&facet.field=" . $parameter_mapping[$facet_name] // oge_agency_name
    . "&facet.limit=10"
    . "&wt=phps";

  /**
   * http://sdw5.reisys.com:18983/solr/checkbook_nycha_dev.public.solr_nycha/terms/?terms.fl=agency_name&terms.fl=oge_agency_name&terms.fl=vendor_name&terms.fl=minority_type_name&terms.fl=revenue_category_name&terms.fl=expenditure_object_name&terms.regex.flag=case_insensitive&terms.regex=%28%2E%2A%20system%2E%2A%29%7C%28%5Esystem%2E%2A%29&wt=phps

   */

  $results = _checkbook_smart_search_solr_results($query);

  if ($results['response']['numFound'] != 0) {
    $facet_results = $results['facet_counts']['facet_fields'][$parameter_mapping[$facet_name]];
    $matches = array();
    foreach ($facet_results as $key => $value) {
      if ($value > 0) {
        $key = (string)$key;
        array_push($matches, array("url" => "", "category" => $facet_name, "label" => $key, "value" => urlencode($key)));
      }
    }

    return drupal_json_output($matches);
  }
  return drupal_json_output([['label' => 'No Matches Found', 'value' => 'No Matches Found']]);
}

/**
 *  Outputs the agency suggestions for the given input.
 *  Returns matching words
 **/
function _checkbook_smart_search_agency_autocomplete()
{
  return _checkbook_smart_search_get_searchstring_autocomplete("agency_names", "agency_name_autocomplete");
}

/**
 *  Outputs the oge suggestions for the given input.
 **/
function _checkbook_smart_search_oge_autocomplete()
{
  return _checkbook_smart_search_get_searchstring_autocomplete("oge_agency_names", "oge_agency_name_autocomplete"); // ln 422
}

/**
 *  Outputs the vendor suggestions for the given input.
 **/
function _checkbook_smart_search_vendor_autocomplete()
{
  return _checkbook_smart_search_get_searchstring_autocomplete("vendor_names", "vendor_name_autocomplete");
}

/**
 *  Outputs the expense category suggestions for the given input.
 **/
function _checkbook_smart_search_expensecategory_autocomplete()
{
  return _checkbook_smart_search_get_searchstring_autocomplete("expense_categories", "expenditure_object_name_autocomplete");
}

/**
 *  Outputs the fiscal year suggestions for the given input.
 **/
function _checkbook_smart_search_fiscalyear_autocomplete()
{
  return _checkbook_smart_search_get_searchstring_autocomplete("fiscal_years", "fiscal_year");
}

/**
 *  Outputs the registered fiscal year suggestions for the given input.
 **/
function _checkbook_smart_search_regfiscalyear_autocomplete()
{
  return _checkbook_smart_search_get_searchstring_autocomplete("registered_fiscal_years", "registered_fiscal_year");
}

/**
 *  Outputs the industry type suggestions for the given input.
 **/
function _checkbook_smart_search_industrytype_autocomplete()
{
  return _checkbook_smart_search_get_searchstring_autocomplete("industry_type_name", "industry_type_name_autocomplete");
}

/** Returns the URL for clear functionality for each Search Term
 * @param $facetName
 * @param null $facetValue
 * @param null $count
 * @return string
 */
function _checkbook_smart_search_clear_url($facetName, $facetValue = null, $count = null)
{

  $searchArgs = $_REQUEST['search_term'];
  $clearArgURL = "/smart_search/?search_term=";
  $clearURL = "";
  $searchTerms = explode('*|*', $searchArgs);
  if ($facetName == 'search_term') {
    for ($i = 1; $i < count($searchTerms); $i++) {
      $search_params = explode('=', $searchTerms[$i]);
      //$search_params[1] = urldecode($search_params[1]);
      $clearURL .= "*|*" . $search_params[0] . '=' . urlencode($search_params[1]);
    }

    $clearArgURL .= $clearURL;
  } else {
    $clearArgURL .= $searchTerms[0];
    $contractsFlag = false;
    for ($i = 1; $i < count($searchTerms); $i++) {
      $facets = explode('=', $searchTerms[$i]);
      if ($facets[0] != $facetName) {
        if ($facetName == 'domains' && $facetValue == 'contracts') {
          if ($facets[0] == 'contract_categories' || $facets[0] == 'contract_status') {
            $clearArgURL .= "";
          } else {
            $clearArgURL .= '*|*' . $facets[0] . '=' . urlencode($facets[1]);
          }

        } else if ($facetName == 'domains' && $facetValue == 'spending') {
          if ($facets[0] == 'spending_categories') {
            $clearArgURL .= "";
          } else {
            $clearArgURL .= '*|*' . $facets[0] . '=' . urlencode($facets[1]);
          }
        } else {
          $clearArgURL .= '*|*' . $facets[0] . '=' . urlencode($facets[1]);
        }
      }

      if ($facets[0] == 'contract_categories' || $facets[0] == 'contract_status' || $facets[0] == 'spending_categories') {
        if ($facets[0] == $facetName)
          $remove_arg = $facets[1];
      }
      if ($facets[0] == 'domains' && ($facets[1] == 'contracts' || $facets[1] == 'spending')) {
        $contractsFlag = true;
      }
    }

    if ($contractsFlag && ($facetName != 'domains' && ($facetValue != 'contracts' || $facetName != 'spending'))) {
      if ($facetName == 'contract_categories' || $facetName == 'contract_status' || $facetName == 'spending_categories') {
        $contQueryString = "";

        $contArgs = explode('~', $remove_arg);
        foreach ($contArgs as $key => $value) {
          if ($value != urlencode($facetValue)) {
            $contQueryString .= urlencode($value) . "~";
          }
        }
        $contQueryString = substr($contQueryString, 0, -1);
        $clearArgURL .= ($count > 1) ? ('*|*' . $facetName . '=' . $contQueryString) : "";
      }
    }
  }

  return $clearArgURL;
}

/** Returns the html entities for string without replacing <em> and </em> tags.
 * @param $string
 * @return mixed|string $string.
 */
function _checkbook_smart_search_str_html_entities($string)
{
  $string = str_replace("<em>", "**|**", $string);
  $string = str_replace("</em>", "**||**", $string);
  $string = htmlentities($string);
  $string = str_replace("**|**", "<em>", $string);
  $string = str_replace("**||**", "</em>", $string);

  return $string;
}

/**
 * This function returns the current NYC year  ...
 * @return year_id    $string = str_replace("^", "\%5E", $string);
 */
function getCurrentYearID()
{
  STATIC $current_nyc_year;
  if (!isset($current_nyc_year)) {
    $current_nyc_year = date("Y");
    $current_month = date("m");
    if ($current_month <= 6)
      $current_nyc_year -= 1;
    $current_nyc_year = _getYearIDFromValue($current_nyc_year);
  }
  return $current_nyc_year;
}

/** Generates smart search export form */
function _checkbook_smart_search_export_form()
{
  echo theme('checkbook_smart_search_export_form');
}

/** Downloads the smart search export data */
function _checkbook_smart_search_export_download()
{
  drupal_add_http_header("Content-Type", "text/csv");
  //drupal_add_http_header("Content-Length",9);
  drupal_add_http_header("Content-Disposition", "attachment; filename=TransactionsData.csv");
  drupal_add_http_header("Pragma", "cache");
  drupal_add_http_header("Expires", "-1");
  _checkbook_smart_search_export_data();
}

/** Exports the smart search export data */
function _checkbook_smart_search_export_data()
{
  $search_terms = explode('*|*', $_REQUEST['search_term']);
  $search_terms[0] = urldecode($search_terms[0]); //"Education"
  $search_terms[0] = ($search_terms[0] == "") ? "*:*" : ucwords(strtolower(CheckbookSolrQuery::escape($search_terms[0])));

  $index = 0;
  $isoge = 0;
  foreach ($search_terms as $search_term) {
    $terms = explode("=", $search_term);
    if ($terms[0] == "domains") {
      $domains = explode("~", $terms[1]);
      $search_terms[$index] = "domains=" . $_REQUEST['domain'];
    }
    if ($terms[0] == "oge_agency_names") {
      $isoge = 1;
    }
    $index += 1;
  }
  global $conf;
  $remaining = $conf['check_book']['datatables']['iTotalDisplayRecords'];
  $page_size = 10000;
  $page_num = 0;

  $fields = _checkbook_smart_search_domain_export_fields($_REQUEST['domain'], $isoge);
  $fl = implode(",", array_keys($fields));
  echo implode(",", array_values($fields)) . "\n";
  $statusflag = false;
  foreach ($search_terms as $id => $keyvaluepair) {
    $keys = explode("=", $keyvaluepair);
    if ($keys[0] == 'contract_status' && $keys[1] == 'registered') {
      $statusflag = true;
    } else if ($keys[0] == 'contract_status' && $keys[1] == 'active') {
      $statusflag = false;
    } else {
      $statusflag = false;
    }
  }

  while ($remaining > 0) {
    $results = _checkbook_smart_search_get_results_csv($search_terms, $page_num, $page_size, $fl, true);
    $page_num += 1;
    $remaining = $remaining - $page_size;
    /*
     * For data which should be N/A and represent a timestamp,
     * the column selected from solr is "1970-01-01 00:00:00 UTC" (epoch time)
     * and we convert it to N/A here.  This is because a null timestamp would return
     * and empty string and is not reliable for casting.
     */
    if (!$statusflag) {
      $results = preg_replace("/Registered,Expense/", "Active,Expense", $results);
      $results = preg_replace("/Registered,Revenue/", "Active,Revenue", $results);
    }

    $results = preg_replace("/1970-01-01T05:00:00Z/", "N/A", $results);
    echo preg_replace("/(\d{4})-(\d{2})-(\d{2})...:..:.../", "$2/$3/$1", $results);
    ob_flush();
    flush();
  }

}

/** Smart search results labels array
 * @param $domain
 * @param bool $IsOge
 * @return array
 */
function _checkbook_smart_search_domain_fields($domain, $IsOge = false)
{
  switch ($domain) {
    case "spending":
      if ($IsOge)
        $parameter_mapping = array(
          "spending_category_name" => "Spending Category",
          "oge_agency_name" => "Agency",
          "department_name" => "Department",
          "vendor_name" => "Payee Name",
          "expenditure_object_name" => "Expense Category",
          "contract_number" => "Contract ID",
          "spending_commodity_line" => "Commodity Line",
          "check_amount" => "Check Amount",
          "check_eft_issued_date" => "Issue Date",
          "spending_entity_contract_number" => "Entity Contract#",
          "spending_budget_name" => "Budget Name",
          "" => "",
        );
      else
        $parameter_mapping = array(
          "spending_category_name" => "Spending Category",
          "agency_name" => "Agency",
          "department_name" => "Department",
          "vendor_name" => "Payee Name",
          "expenditure_object_name" => "Expense Category",
          "industry_type_name" => "Industry",
          "contract_number" => "Contract ID",
          "check_amount" => "Check Amount",
          "check_eft_issued_date" => "Issue Date",
          "is_prime_or_sub" => "Sub Vendor",
          "minority_type_name" => "M/WBE Category",
          "prime_vendor_name" => "Associated Prime Vendor",
          "" => ""
        );
      break;
    case "contracts":
      if ($IsOge) {
        $parameter_mapping = array(
          "contract_status" => "Status",
          "contract_category_name" => "Category",
          "vendor_name" => "Prime Vendor",
          "oge_contracting_agency_name" => "Agency",
          "contract_number" => "Contract ID",
          "contract_commodity_line" => "Commodity Line",
          "contract_purpose" => "Purpose",
          "parent_contract_number" => "Parent Contract ID",
          "award_method_name" => "Award Method",
          "contract_type" => "Contract type",
          "current_amount" => "Current Amount",
          "original_amount" => "Original Amount",
          "start_date_orig" => "Start date",
          "end_date_orig" => "End Date",
          "pin" => "PIN",
          "apt_pin" => "APT PIN",
          "expense_category_name" => "Expense Category",
          "contract_entity_contract_number" => "Entity Contract#",
          "contract_budget_name" => "Budget Name",
          "" => ""
        );
      } else {
        $parameter_mapping = array(
          "contract_status" => "Status",
          "contract_category_name" => "Category",
          "vendor_name" => "Vendor",
          "agency_name" => "Agency",
          "contract_number" => "Contract ID",
          "version_number" => "Version Number",
          "contract_purpose" => "Purpose",
          "parent_contract_number" => "Parent Contract ID",
          "award_method_name" => "Award Method",
          "contract_type" => "Contract type",
          "current_amount" => "Current Amount",
          "original_amount" => "Original Amount",
          "start_date_orig" => "Start date",
          "end_date_orig" => "End Date",
          "pin" => "PIN",
          "apt_pin" => "APT PIN",
          "registration_date" => "Registration date",
          "expense_category_name" => "Expense Category",
          "industry_type_name" => "Industry",
          "received_date" => "Received date",
          "minority_type_name" => "M/WBE Category",
          "prime_vendor_name" => "Associated Prime Vendor",
          "" => "");
      }
      break;
    case "payroll":
      if ($IsOge) {
        $parameter_mapping = array(
          "civil_service_title" => "Title",
          "oge_agency_name" => "Agency",
          "pay_frequency" => "Pay Frequency",
          "pay_date" => "Pay Date",
          "gross_pay" => "Gross Pay",
          "annual_salary" => "Annual Salary",
          "base_pay" => "Base Pay",
          "hourly_rate" => "Hourly Rate",
          "other_payments" => "Other Payments",
          "overtime_pay" => "Overtime Payments",
          "payroll_type" => "Payroll Type",
          "" => ""
        );
      } else {
        $parameter_mapping = array(
          "civil_service_title" => "Title",
          "agency_name" => "Agency",
          "pay_frequency" => "Pay Frequency",
          "pay_date" => "Pay Date",
          "gross_pay" => "Gross Pay",
          "annual_salary" => "Annual Salary",
          "base_pay" => "Base Pay",
          "hourly_rate" => "Hourly Rate",
          "other_payments" => "Other Payments",
          "overtime_pay" => "Overtime Payments",
          "payroll_type" => "Payroll Type",
          "" => ""
        );
      }
      break;
    case "budget":
      $parameter_mapping = ["agency_name" => "Agency",
        "department_name" => "Department Name",
        "expenditure_object_name" => "Expense Category",
        "budget_code_name" => "Budget Category",
        "adopted_amount" => "Adopted",
        "current_budget_amount" => "Modified",
        "pre_encumbered_amount" => "Pre Encumbered",
        "encumbered_amount" => "Encumbered",
        "accrued_expense_amount" => "Accrued Expense",
        "cash_expense_amount" => "Cash Payments",
        "post_closing_adjustment_amount" => "Post Adjustments",
        "fiscal_year" => "Year",
      ];
      break;
    case "revenue":
      $parameter_mapping = ["agency_name" => "Agency Name",
        "fiscal_year" => "Budget Fiscal Year",
        "display_fiscal_year" => "Fiscal Year",
        "revenue_category_name" => "Revenue Category",
        "revenue_source_name" => "Revenue Source",
        "funding_class_name" => "Funding Class",
        "adopted_amount" => "Adopted",
        "current_budget_amount" => "Modified",
        "posting_amount" => "Recognized",
        "" => "",
      ];
      break;
  }
  return $parameter_mapping;
}


/**
 * @param $domain
 * @param $IsOge
 * @return array
 */
function _checkbook_smart_search_domain_export_fields($domain, $IsOge)
{
  switch ($domain) {
    case "spending":
      if ($IsOge) {
        $parameter_mapping = [
          "spending_category_name" => "Spending Category",
          "agency_name_export" => "Agency",
          "department_name" => "Department",
          "vendor_name" => "Payee Name",
          "expenditure_object_name" => "Expense Category",
          "contract_number" => "Contract ID",
          "spending_commodity_line" => "Commodity Line",
          "check_amount" => "Check Amount",
          "check_eft_issued_date_export" => "Issue Date",
          "spending_entity_contract_number" => "Entity Contract#",
          "spending_budget_name" => "Budget Name",
          "" => "",];
      } else {
        $parameter_mapping = [
          "spending_category_name" => "Spending Category",
          "agency_name_export" => "Agency",
          "department_name" => "Department",
          "vendor_name" => "Payee Name",
          "expenditure_object_name" => "Expense Category",
          "industry_type_name" => "Industry",
          "contract_number" => "Contract ID",
          "check_amount" => "Check Amount",
          "check_eft_issued_date_export" => "Issue Date",
          "minority_type_name" => "M/WBE Category",
          "is_prime_or_sub" => "Sub Vendor",
          "prime_vendor_name" => "Associated Prime Vendor",
          "" => "",];
      }
      break;
    case "contracts":
      if ($IsOge) {
        $parameter_mapping = [
          "contract_status" => "Status",
          "contract_category_name" => "Category",
          "vendor_name" => "Prime Vendor",
          "agency_name_export" => "Agency",
          "contract_number" => "Contract ID",
          "contract_commodity_line" => "Commodity Line",
          "contract_purpose" => "Purpose",
          "parent_contract_number" => "Parent Contract ID",
          "award_method_name" => "Award Method",
          "contract_type" => "Contract type",
          "current_amount" => "Current Amount",
          "original_amount" => "Original Amount",
          "start_date_orig" => "Start date",
          "end_date_orig" => "End Date",
          "pin" => "PIN",
          "apt_pin" => "APT PIN",
          "expense_category_name" => "Expense Category",
          "contract_entity_contract_number" => "Entity Contract#",
          "contract_budget_name" => "Budget Name",
        ];
      } else {
        $parameter_mapping = [
          "contract_status" => "Status",
          "contract_category_name" => "Category",
          "vendor_record_type" => "Vendor Record Type",
          "vendor_name" => "Vendor",
          "agency_name_export" => "Agency",
          "contract_number" => "Contract ID",
          "version_number" => "Version Number",
          "contract_purpose" => "Purpose",
          "parent_contract_number" => "Parent Contract ID",
          "award_method_name" => "Award Method",
          "contract_type" => "Contract type",
          "current_amount" => "Current Amount",
          "original_amount" => "Original Amount",
          "start_date_orig" => "Start date",
          "end_date_orig" => "End Date",
          "pin" => "PIN",
          "apt_pin" => "APT PIN",
          "registration_date" => "Registration date",
          "expense_category_name" => "Expense Category",
          "industry_type_name" => "Industry",
          "received_date" => "Received date",
          "minority_type_name" => "M/WBE Category",
          "prime_vendor_name" => "Associated Prime Vendor",
        ];
      }
      break;
    case "payroll":
      $parameter_mapping = [
        "civil_service_title" => "Title",
        "agency_name" => "Agency",
        "pay_frequency" => "Pay Frequency",
        "pay_date" => "Pay Date",
        "gross_pay" => "Gross Pay",
        "annual_salary" => "Annual Salary",
        "base_pay" => "Base Pay",
        "hourly_rate" => "Hourly Rate",
        "other_payments" => "Other Payments",
        "overtime_pay" => "Overtime Payments",
        "payroll_type" => "Payroll Type",
      ];
      break;
    case "budget":
      $parameter_mapping = ["agency_name" => "Agency",
        "department_name" => "Department Name",
        "expenditure_object_name" => "Expense Category",
        "budget_code_name" => "Budget Category",
        "adopted_amount" => "Adopted",
        "current_budget_amount" => "Modified",
        "pre_encumbered_amount" => "Pre Encumbered",
        "encumbered_amount" => "Encumbered",
        "accrued_expense_amount" => "Accrued Expense",
        "cash_expense_amount" => "Cash Payments",
        "post_closing_adjustment_amount" => "Post Adjustments",
        "fiscal_year" => "Year",
      ];
      break;
    case "revenue":
      $parameter_mapping = ["agency_name" => "Agency Name",
        "fiscal_year" => "Budget Fiscal Year",
        "display_fiscal_year" => "Fiscal Year",
        "revenue_category_name" => "Revenue Category",
        "revenue_source_name" => "Revenue Source",
        "funding_class_name" => "Funding Class",
        "adopted_amount" => "Adopted",
        "current_budget_amount" => "Modified",
        "posting_amount" => "Recognized",
        "" => "",
      ];
      break;
  }
  return $parameter_mapping;
}

/** Exports the smart search export data
 * @param $query
 * @return false|string
 */
function _checkbook_smart_search_solr_results_csv($query)
{
  $solr = new CheckbookSolr();
  return $solr->request_csv($query);
}

/** Exports the smart search export data
 * @param $search_terms
 * @param $page_no
 * @param $no_of_results
 * @param string $fl
 * @param bool $skip_header
 * @return false|string
 */
function _checkbook_smart_search_get_results_csv($search_terms, $page_no, $no_of_results, $fl = "*", $skip_header = false)
{
  $search_string = _checkbook_smart_search_get_results_query_search_term($search_terms);
  $domain = $_REQUEST['domain'];
  $query = "select/?q=" . $search_string
    . "&sort=domain_ordering+asc,date_ordering+desc"
    . "&start=" . ($page_no * $no_of_results)
    . "&wt=csv"
    . "&rows=" . $no_of_results;

  $query = $query . "&fq=domain:" . $domain;
  $query = $query . "&fl=" . $fl;

  if ($skip_header) {
    $query = $query . "&csv.header=false";
  }
  return _checkbook_smart_search_solr_results_csv($query);
}


/**
 * @param string $domain
 * @throws Exception
 */
function _checkbook_smart_search_ajax_results(string $domain = 'citywide')
{
//    $page = $_GET['page'];
  $search_terms = explode('*|*', $_REQUEST['search_term']);
  $search_terms[0] = urldecode($search_terms[0]); //"Education"

  $search_terms[0] = ($search_terms[0] == "") ? "*:*" : ucwords(strtolower(CheckbookSolrQuery::escape($search_terms[0])));

  $page_no = ($_REQUEST['page']) ? $_REQUEST['page'] : 0; // 0
  $no_of_results = 10;
  $search_results = _checkbook_smart_search_results($search_terms, $page_no, $no_of_results, $domain);
  print theme('ajax_results', array('search_results' => $search_results['search_results']));

}

/**
 * @param $reqParams
 * @return bool|string
 */
function get_vendor_type_mapping($reqParams)
{
  $vtypeParam = explode('~', $reqParams);
  $reqParams = '';
  foreach ($vtypeParam as $vtype) {
    if ($vtype == 'pv') {
      $reqParams .= 'p~pm~';
    }
    if ($vtype == 'sv') {
      $reqParams .= 's~sm~';
    }
    if ($vtype == 'mv') {
      $reqParams .= 'sm~pm~';
    }
  }
  $reqParams = substr($reqParams, 0, -1);
  return $reqParams;
}

/**
 * @param $reqParams
 * @return string
 */
function get_contract_status_mapping($reqParams)
{
  switch ($reqParams) {

    case 'pending':
      return 'pending';
      break;

    case 'registered':
    case 'active':
    default:
      return 'registered';
      break;

  }
}

