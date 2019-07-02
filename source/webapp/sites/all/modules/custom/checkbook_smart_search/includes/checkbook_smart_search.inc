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
function _checkbook_smart_search_load_unchecked_facets(string $solr_datasource, CheckbookSolrQuery $solr_query, string $facet): array
{
  $solr = CheckbookSolr::getInstance($solr_datasource);

  $facet_query = clone $solr_query;
  $facet_query
    ->setRows(0)
    ->clearFacets()
    ->addFacet($facet)
    ->excludeFqTermValues($facet);
  $facet_results = $solr->request_phps('select/?' . $facet_query->buildQuery());

  return $facet_results['facet_counts']['facet_fields'][$facet] ?? [];
}

/**
 * @param string $solr_datasource
 * @throws Exception
 */
function _checkbook_smart_search_ajax_results(string $solr_datasource = 'citywide')
{
  $solr_query = new CheckbookSolrQuery($_REQUEST['search_term'], 10, $_REQUEST['page'] ?? 0);

  $solr_query
    ->setSort('domain_ordering asc,date_ordering desc');

  $solr = CheckbookSolr::getInstance($solr_datasource);
  $search_results = $solr->request_phps('select/?' . $solr_query->buildQuery());

  print theme('ajax_results', [
    'solr_datasource' => $solr_datasource,
    'search_results' => $search_results
  ]);
}

/** Returns results for smart search
 * @param string $solr_datasource
 * @return mixed|string
 * @throws Exception
 */
function _checkbook_smart_search_get_results(string $solr_datasource = 'citywide')
{
  $solr_query = new CheckbookSolrQuery($_REQUEST['search_term'], 10, $_REQUEST['page'] ?? 0);

  $solr_query
    ->setSort('domain_ordering asc,date_ordering desc');

  $datasource_facets = (array)CheckbookSolr::getFacetConfigByDatasource($solr_datasource);

  $selected_facets = $solr_query->getSelectedFacets();

  foreach ($datasource_facets as $facet_name => $facet) {
    $solr_query->addFacet($facet_name);
    if ($facet->sub && in_array($facet_name, array_keys($selected_facets))) {
      foreach ($selected_facets[$facet_name] as $facet_value) {
        if ($domain_facets = $facet->sub->$facet_value ?? false) {
          $solr_query->addFacets($domain_facets);
        }
      }
    }
  }

  $search_terms = explode('*|*', $_REQUEST['search_term']); // array("Education", "domains=Payroll~Spending", "fiscal_years=2014~2013")
  $reg_flag = false;
//  $act_flag = false;
  foreach ($search_terms as $id => $keyvaluepair) {
    $keys = explode("=", $keyvaluepair);
    if ($keys[1] == 'registered') {
      $reg_flag = true;
    }
  }

  if ($reg_flag && in_array('registered_fiscal_years', array_keys($datasource_facets))) {
    $solr_query->addFacet('registered_fiscal_years');
  } else {
    $solr_query->addFacet('fiscal_years');
  }

  $selected_facets = $solr_query->getSelectedFacets();

  $solr = CheckbookSolr::getInstance($solr_datasource);
  $search_results = $solr->request_phps('select/?' . $solr_query->buildQuery());

  $vendor_number = count($search_results['facet_counts']['facet_fields']['vendor_name'] ?? []);
  $vendor_pages = floor($vendor_number / 100);
  drupal_add_js(array('checkbook_smart_search' => array('vendor_pages' => $vendor_pages)), 'setting');

  $expense_categories_number = 0;
  if ($search_results['facet_counts']['facet_fields']['expenditure_object_name'] ?? false) {
    $expense_categories_number = sizeof($search_results['facet_counts']['facet_fields']['expenditure_object_name']);
  }

  $expense_category_pages = floor($expense_categories_number / 100);
  drupal_add_js(array('checkbook_smart_search' => array('expense_category_pages' => $expense_category_pages)), 'setting');

  foreach($search_results['facet_counts']['facet_fields'] as $facet => $v){
    if($sort = $datasource_facets->$facet->sort_by_key??0){
      if ($sort > 0) {
        ksort($search_results['facet_counts']['facet_fields'][$facet]);
      } else {
        krsort($search_results['facet_counts']['facet_fields'][$facet]);
      }
    }
  }

  if ($selected_facets) {
    foreach (array_keys($selected_facets) as $facet) {
      $unchecked_facet_fields = _checkbook_smart_search_load_unchecked_facets($solr_datasource, $solr_query, $facet);
      if($sort = $datasource_facets->$facet->sort_by_key??0){
        if ($sort > 0) {
          ksort($unchecked_facet_fields);
        } else {
          krsort($unchecked_facet_fields);
        }
      }
      if ($unchecked_facet_fields) {
        $search_results['facet_counts']['facet_fields'][$facet] =
          ($search_results['facet_counts']['facet_fields'][$facet] ?? []) + ($unchecked_facet_fields ?? []);
      }
    }
  }

  return theme('smart_search_results', [
    'solr_datasource' => $solr_datasource,
    'search_results' => $search_results,
    'selected_facet_results' => $selected_facets,
  ]);
}

/** Exports the smart search export data
 * @param string $solr_datasource
 * @param $search_terms
 * @param $page_no
 * @param $no_of_results
 * @param string $fl
 * @param bool $skip_header
 * @return false|string
 */
function _checkbook_smart_search_get_results_csv(string $solr_datasource, string $search_terms, $page_no, $no_of_results, $fl = "*", $skip_header = false)
{
  $solr_query = new CheckbookSolrQuery($search_terms, $no_of_results, $page_no);
  $solr_query
    ->setWt('csv')
    ->setSort('domain_ordering asc,date_ordering desc');

  $domain = $_REQUEST['domain'];
  $solr_query->setFq('domain', 'domain:' . $domain);

  $query = $solr_query->buildQuery();
  $query .= '&fl=' . $fl;

  /**
   * DANGER:
   * SOLR 7 (gptext) doesn't seem to pick this up
   */
  if ($skip_header) {
    $query = $query . "&csv.header:false";
  }

  $solr = CheckbookSolr::getInstance($solr_datasource);
  return $solr->request_csv('select/?' . $query);
}

/** Generates smart search export form
 * @param string $solr_datasource
 * @throws Exception
 */
function _checkbook_smart_search_export_form(string $solr_datasource)
{
  if (!$solr_datasource) {
    echo 'unknown datasource';
  } else {
    echo theme('checkbook_smart_search_export_form', [
      'solr_datasource' => $solr_datasource
    ]);
  }
}

/** Downloads the smart search export data
 * @param string $solr_datasource
 */
function _checkbook_smart_search_export_download(string $solr_datasource)
{
  $domain = ucfirst($_REQUEST['domain']);
  drupal_add_http_header("Content-Type", "text/csv");
  //drupal_add_http_header("Content-Length",9);
  drupal_add_http_header("Content-Disposition", "attachment; filename={$solr_datasource}{$domain}.csv");
  drupal_add_http_header("Pragma", "cache");
  drupal_add_http_header("Expires", "-1");
  _checkbook_smart_search_export_data($solr_datasource);
}

/** Exports the smart search export data
 * @param string $solr_datasource
 */
function _checkbook_smart_search_export_data(string $solr_datasource)
{
  global $conf;
  $remaining = $conf['check_book']['datatables']['iTotalDisplayRecords'];
  $page_size = 10000;
  $page_num = 0;

  $fields = (array)CheckbookSolr::getExportFields($solr_datasource, $_REQUEST['domain']);

  $fl = implode(",", array_keys($fields));
  echo implode(",", array_values($fields)) . "\n";

  while ($remaining > 0) {
    $result = _checkbook_smart_search_get_results_csv($solr_datasource, $_REQUEST['search_terms'] ?? '', $page_num, $page_size, $fl, true);
    $page_num += 1;
    $remaining = $remaining - $page_size;

    /**
     * SOLR 7 (gptext) doesn't seem to understand csv.header=false
     */
    $chunk = 'huerbuer'.substr($result, 0, 200);
    $column_keys = array_keys($fields);
    if (strpos($chunk, "\"{$column_keys[0]}\"")
      || strpos($chunk, "\"{$column_keys[1]}\"")
      || strpos($chunk, "\"{$column_keys[2]}\"")
    ) {
      // csv header detected
      // removing first line manually
      $result = preg_replace('/^.+\n/', '', $result);
    }

//    last page reached => exit
    if (!$result){
      $remaining=0;
      continue;
    }

    $result = str_replace("Registered,Expense", "Active,Expense", $result);
    $result = str_replace("Registered,Revenue", "Active,Revenue", $result);

    $result = preg_replace("/1970-01-01T05:00:00Z/", "N/A", $result);
    echo preg_replace("/(\d{4})-(\d{2})-(\d{2})...:..:.../", "$2/$3/$1", $result);
    ob_flush();
    flush();
  }
  exit();
}

/**
 *  Outputs the suggestions for the given input.
 *  Returns the matching words
 *
 * @param string $solr_datasource
 */
function _checkbook_smart_search_autocomplete_main_input($solr_datasource = 'citywide')
{
  $search_term = htmlspecialchars_decode($_REQUEST['term'], ENT_QUOTES);
  $search_term = strtolower($search_term);

  $search_term = '(.* ' . $search_term . '.*)|(^' . $search_term . '.*)';

  $query = new CheckbookSolrQuery();

  $autocomplete_categories = (array)CheckbookSolr::getAutocompleteTerms($solr_datasource);

  $query
    ->addTermFields(array_keys($autocomplete_categories))
    ->setTermsRegex($search_term);

  $solr = new CheckbookSolr($solr_datasource);
  $contents = $solr->requestTerms($query);


  $smart_search_url = '/smart_search/' . $solr_datasource;

  $matches_render = [];

  foreach($contents['terms'] as $term=>$matches){
    foreach($matches as $match=>$count){
      $url = $smart_search_url . "?search_term=*|*{$term}=";
      $matches_render[] = [
        "url" => $url,
        "category" => $autocomplete_categories[$term],
        "label" => $match,
        "value" => urlencode($match)];
    }
  }

  if (!sizeof($matches_render)){
    $matches_render = [
      "url" => "",
      "label" => '<span>' . "No matches found" . '</span>',
      'value' => 'No matches found'
    ];
  }

//
//  $count = 0;
//  foreach ($contents['terms'] as $key => $value) {
//    switch ($key) {
//      case 'agency_name':
//        foreach ($value as $a => $b) {
//          if (!in_array($a, $contents['terms']['oge_agency_name'])) {
//            $count++;
//            $url = $smart_search_url . "?search_term=*|*agency_names=";
//            $matches[] = ["url" => $url, "category" => "Agencies", "label" => $a, "value" => urlencode($a)];
//          }
//        }
//        break;
//      case 'oge_agency_name':
//        foreach ($value as $a => $b) {
//          $count++;
//          $url = $smart_search_url . "?search_term=*|*oge_agency_names=";
//          $matches[] = array("url" => $url, "category" => "Other Government Entities", "label" => $a, "value" => urlencode($a));
//        }
//        break;
//      case 'vendor_name':
//        foreach ($value as $a => $b) {
//          $count++;
//          $url = $smart_search_url . "?search_term=*|*vendor_names=";
//          array_push($matches, array("url" => $url, "category" => "Vendors (Prime Vendor, Sub Vendor, M/WBE Vendor)", "label" => $a, "value" => urlencode($a)));
//        }
//        break;
//      case 'minority_type_name':
//        foreach ($value as $a => $b) {
//          $count++;
//          $url = $smart_search_url . "?search_term=*|*minority_type_name=";
//          array_push($matches, array("url" => $url, "category" => "M/WBE Category", "label" => $a, "value" => urlencode($a)));
//        }
//        break;
//      case 'expenditure_object_name':
//        foreach ($value as $a => $b) {
//          $count++;
//          $url = $smart_search_url . "?search_term=*|*expense_categories=";
//          array_push($matches, array("url" => $url, "category" => "Expense Categories", "label" => $a, "value" => urlencode($a)));
//        }
//        break;
//    }
//
//  }
//  if ($count == 0) {
//    array_push($matches, ["url" => "", "label" => '<span>' . "No matches found" . '</span>', 'value' => 'No matches found']);
//  }

  return drupal_json_output($matches_render);
}

/**
 * @param string $solr_datasource
 * @param string $facet
 * @return array
 */
function _checkbook_autocomplete(string $solr_datasource, string $facet): array
{
  $solr_query = new CheckbookSolrQuery($_REQUEST['search_term'] ?? '');

  $solr_query->setFqAutocompleteTerm($facet, $_REQUEST['term'] ?? '');

  $solr = new CheckbookSolr($solr_datasource);
  $results = $solr->request_phps('select/?' . $solr_query);

  if ($results['response']['numFound'] != 0) {
    $facet_results = array_shift($results['facet_counts']['facet_fields']);
    $matches = [];
    foreach (array_keys($facet_results) as $key) {
      array_push($matches, ["url" => "", "category" => $facet, "label" => $key, "value" => $key]);
    }

    return $matches;
  }
  return [['label' => 'No Matches Found', 'value' => '']];
}

/**
 * @param string $solr_datasource
 * @param string $facet
 */
function _checkbook_smart_search_autocomplete(string $solr_datasource, string $facet)
{
  $data = _checkbook_autocomplete($solr_datasource, $facet);
  foreach ($data as &$line) {
    $line['value'] = urlencode($line['value']);
  }
  drupal_json_output($data);
}

/**
 * @param string $solr_datasource
 * @param string $facet
 */
function _checkbook_advanced_search_autocomplete(string $solr_datasource, string $facet)
{
  $data = _checkbook_autocomplete($solr_datasource, $facet);
  drupal_json_output($data);

}

/**
 * @param $results
 * @return array
 */
function _checkbook_vendor_type_prepare_labels($results)
{
  $pv = $results['p'] ?? 0 + $results['pm'] ?? 0;
  $sv = $results['s'] ?? 0 + $results['sm'] ?? 0;
  $mv = $results['sm'] ?? 0 + $results['pm'] ?? 0;
  $return = [];
  if ($pv) {
    $return['pv'] = ['Prime Vendor', $pv];
  }
  if ($sv) {
    $return['sv'] = ['Sub Vendor', $sv];
  }
  if ($mv) {
    $return['mv'] = ['M/WBE Vendor', $mv];
  }
  return $return;
}

/**
 *
 *
 * ██╗  ██╗███████╗██╗     ██╗         ██████╗ ███████╗██╗      ██████╗ ██╗    ██╗    ████████╗██╗  ██╗██╗███████╗    ██╗     ██╗███╗   ██╗███████╗
 * ██║  ██║██╔════╝██║     ██║         ██╔══██╗██╔════╝██║     ██╔═══██╗██║    ██║    ╚══██╔══╝██║  ██║██║██╔════╝    ██║     ██║████╗  ██║██╔════╝
 * ███████║█████╗  ██║     ██║         ██████╔╝█████╗  ██║     ██║   ██║██║ █╗ ██║       ██║   ███████║██║███████╗    ██║     ██║██╔██╗ ██║█████╗
 * ██╔══██║██╔══╝  ██║     ██║         ██╔══██╗██╔══╝  ██║     ██║   ██║██║███╗██║       ██║   ██╔══██║██║╚════██║    ██║     ██║██║╚██╗██║██╔══╝
 * ██║  ██║███████╗███████╗███████╗    ██████╔╝███████╗███████╗╚██████╔╝╚███╔███╔╝       ██║   ██║  ██║██║███████║    ███████╗██║██║ ╚████║███████╗
 * ╚═╝  ╚═╝╚══════╝╚══════╝╚══════╝    ╚═════╝ ╚══════╝╚══════╝ ╚═════╝  ╚══╝╚══╝        ╚═╝   ╚═╝  ╚═╝╚═╝╚══════╝    ╚══════╝╚═╝╚═╝  ╚═══╝╚══════╝
 *
 *
 */


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
 * @return string    $string = str_replace("^", "\%5E", $string);
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
