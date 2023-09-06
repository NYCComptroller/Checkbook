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


use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;
use Drupal\checkbook_solr\CheckbookSolr;
use Drupal\checkbook_solr\CheckbookSolrQuery;
use Symfony\Component\HttpFoundation\Response;

/** Submit handler for smart search
 * @param $form
 * @param $form_state
 */
function _checkbook_smart_search_submit($form, &$form_state) {
  $solr_datasource = Datasource::getCurrentSolrDatasource();
  $search_term = trim($form['search_box']['#value']);
  if ($search_term == 'Type ahead search') {
    $search_term = '';
  }
  $search_term = ($search_term) ? $search_term : '';
  $form_state->setRedirect('checkbook_smart_search.get_results', [
    'solr_datasource' => $solr_datasource,
      'search_term' => urlencode($search_term)
    ]
  );
}

/**
 * @param array $query
 * @param array $selectedFacets
 * @return array
 */
function getRegisteredContractsQuery($query, $selectedFacets){
  if(isset($selectedFacets['domain']) && in_array('contracts', $selectedFacets['domain'])){
    //Registered contracts count
    $query = str_replace('&fq=contract_status:"pending"','',$query);
    $query = str_replace('&fq=facet_year_array','&fq=registered_fiscal_year',$query);
    $query .= (strpos($query, '&fq=contract_status:registered') !== false) ? '&fq=contract_status:registered' : '';
    return $query;
  }else{
    return;
  }
  return;
}

/**
 * @param array $query
 * @param array $selectedFacets
 * @return array
 */
function getActiveContractsQuery($query, $selectedFacets){
  if(isset($selectedFacets['domain']) && in_array('contracts', $selectedFacets['domain'])){
    //Active contracts count
    $query = str_replace('&fq=contract_status:"pending"','',$query);
    $query = str_replace('&fq=registered_fiscal_year','&fq=facet_year_array',$query);
    $query .= (strpos($query, '&fq=contract_status:registered') !== false) ? '&fq=contract_status:registered' : '';
    return $query;
  }else{
    return;
  }
}

/**
 * @param array $search_results
 * @param array $datasource_facets
 * @return array
 */
function _ckbk_ss_sort_results(array $search_results, array $datasource_facets){
  /**
   * Sorting facet by value, like Fiscal Year desc
   * Values are set in checkbook_solr/src/Config/___.json .
   * $facets: $datasource_facets[$facet]->sort_by_key
   * domain :
   * contract_category_name :
   * contract_status :
   * spending_category_name :
   * vendor_name :
   * expenditure_object_name :
   * facet_year_array :-1
   * registered_fiscal_year :-1
   * contract_number :
   */
  foreach ($search_results['facet_counts']['facet_fields'] as $facet => $v) {
    // @todo: $datasource_facets should be an array according to function declaration.
    if( isset($datasource_facets)
      && isset($datasource_facets[$facet])
      && isset($datasource_facets[$facet]->sort_by_key)
    ){
      // Sort by key.
      krsort($search_results['facet_counts']['facet_fields'][$facet]);
    } else {
      // Sort by value.
      // ksort($search_results['facet_counts']['facet_fields'][$facet]);
      arsort($search_results['facet_counts']['facet_fields'][$facet]);
    }
  }
  return $search_results;
}

/**
 * @param array $search_results
 * @return array
 */
function _ckbk_remove_empty_intervals(array $search_results){
  /**
   * Manually getting rid of empty facet_intervals values because
   * facet.mincount does not work with intervals/ranges in solr
   */
  if (isset($search_results['facet_counts']['facet_intervals'])) {
    foreach ($search_results['facet_counts']['facet_intervals'] as $facet => $data) {
      foreach ($data as $k => $v) {
        if (!$v) {
          unset($search_results['facet_counts']['facet_intervals'][$facet][$k]);
        }
      }
      if (!sizeof($search_results['facet_counts']['facet_intervals'][$facet])) {
        unset($search_results['facet_counts']['facet_intervals'][$facet]);
      }
    }
    if (!sizeof($search_results['facet_counts']['facet_intervals'])) {
      unset($search_results['facet_counts']['facet_intervals']);
    }
  }
  return $search_results;
}

/**
 * @param array $search_results
 */
function _inject_smart_search_drupal_settings(array $search_results){
  $vendor_number = count(($search_results['facet_counts']['facet_fields']['vendor_name']) ? $search_results['facet_counts']['facet_fields']['vendor_name'] : []);
  $vendor_pages = floor($vendor_number / 100);

// drupal_add_js(array('checkbook_smart_search' => array('vendor_pages' => $vendor_pages)), 'setting');
  $build['#attached']['drupalSettings']['checkbook_smart_search']['vendor_pages'] = $vendor_pages;

  $expense_categories_number = 0;
  if (($search_results['facet_counts']['facet_fields']['expenditure_object_name']) ? $search_results['facet_counts']['facet_fields']['expenditure_object_name'] : false) {
    $expense_categories_number = sizeof($search_results['facet_counts']['facet_fields']['expenditure_object_name']);
  }

  $expense_category_pages = floor($expense_categories_number / 100);

// drupal_add_js(array('checkbook_smart_search' => array('expense_category_pages' => $expense_category_pages)), 'setting');
  $build['#attached']['drupalSettings']['checkbook_smart_search']['expense_category_pages'] = $expense_category_pages;

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
function _checkbook_smart_search_get_results_csv(string $solr_datasource, string $search_terms, $page_no, $no_of_results, $fl = "*", $skip_header = false) {
  $solr_query = new CheckbookSolrQuery($solr_datasource, $search_terms, $no_of_results, $page_no);
  $solr_query
    ->setWt('csv');

  $domain = \Drupal::request()->query->get('domain');
  $solr_query->setFq('domain', 'domain:' . $domain);

  $query = $solr_query->buildQuery();
  $query .= '&fl=' . $fl;

  /**
   * DANGER:
   * SOLR 7 (gptext) doesn't seem to pick this up
   */
  if ($skip_header) {
    $query = $query . "&csv.header=false";
  }

  $solr = CheckbookSolr::getInstance($solr_datasource);
  return $solr->request_csv('select/?' . $query);
}

/**
 * Processes hyphen fields and special case values for export results
 * @param string $result
 * @param $domain
 * @return string $modified_result
 */
function adjustExportResults($result,$domain){
  // Write to browser export file
  //$output = fopen('php://output', 'w');
  $lines = explode("\n", $result);
  $headers = str_getcsv(array_shift($lines));
  $lines = str_replace("\,", " ", $lines);
  $lines = preg_replace("/1970-01-01T05:00:00Z/", "N/A", $lines);
  $lines = preg_replace("/(\d{4})-(\d{2})-(\d{2})...:..:.../", "$2/$3/$1", $lines);
  $lines= str_replace("Registered,Expense", "Active,Expense", $lines);
  $lines = str_replace("Registered,Revenue", "Active,Revenue", $lines);
  $return = '';
  foreach ($lines as $line) {
    $row = array();
    $data = str_getcsv($line);
    foreach ($data as $key => $field) {
      switch($domain) {
        case "nycha_contracts":
          if ($headers[$key] == 'agreement_type_name') {
            if (strtoupper($field) == 'PURCHASE ORDER') {
              $date_fields = array("start_date", "end_date");
            }
          }
          if ($headers[$key] == 'record_type') {
            if ($field == 'Agreement') {
              $hyphen_fields = array("release_number", "line_number", "commodity_category_name", "item_description", "item_qty_ordered", "shipment_number", "responsibility_center_name",
                "release_line_total_amount", "release_line_original_amount", "release_line_spend_to_date", "release_approved_date", "release_total_amount", "release_original_amount",
                "release_spend_to_date", "location_name", "grant_name", "expenditure_type_name", "funding_source_name", "program_phase_code", "gl_project_name");
            } elseif ($field == 'Release') {
              $hyphen_fields = array("number_of_releases", "line_number", "commodity_category_name", "item_description", "item_qty_ordered", "shipment_number", "responsibility_center_name", "release_line_total_amount",
                "release_line_original_amount", "release_line_spend_to_date", "agreement_total_amount", "agreement_original_amount", "agreement_spend_to_date", "location_name",
                "grant_name", "expenditure_type_name", "funding_source_name", "program_phase_code", "gl_project_name");
            } elseif ($field == 'Line') {
              $hyphen_fields = array("number_of_releases", "release_total_amount", "release_original_amount", "release_spend_to_date", "agreement_total_amount", "agreement_original_amount", "agreement_spend_to_date");
            }
          }
          break;
        case "nycha_spending":
          $hyphen_fields=[];
          array_push($hyphen_fields,"agreement_type_name", "contract_number", "release_number", "contract_purpose","invoice_number");
          $amount_hyphen_fields = array("check_invoice_net_amount", "distribution_line_number", "invoice_line_number");
          if ($headers[$key] == 'document_id') {
            if ($field == null) {
              $field = "N/A";
            }
          }
          if (($headers[$key] == 'spending_category')) {
            if (($field == 'Other') || ($field == 'Section 8')) {
              array_pop($hyphen_fields);
              $category_hyphen = array("industry_type_name");
            } elseif ($field == 'Payroll') {
              $category_hyphen = array( "contract_spent_amount","industry_type_name", "funding_source_name", "responsibility_center_name", "program_phase_name", "gl_project_name");
            } else{
              $hyphen_fields = [];
            }
          }
          break;
        case "nycha_payroll":
          $amount_hyphen_fields = array("annual_salary", "hourly_rate", "daily_wage");
          break;
        case "nycha_revenue":
          if ($headers[$key] == 'adopted_amount') {
            $adopted_amount = $field;
          }
          if ($headers[$key] == 'modified_amount') {
            $field = $adopted_amount;
          }
          break;
        case "nycha_budget":
          if ($headers[$key] == 'adopted_budget') {
            $adopted_amount = $field;
          }
          if ($headers[$key] == 'modified_budget') {
            $field = $adopted_amount;
          }
          break;
        case "citywide_payroll":
          // Display daily wage is hourly rate is zero or '-' if both are zero
          if ($headers[$key] == 'daily_wage') {
            if ($field <= 0) {
              $field = '-';
            }
            $daily_wage = $field;
          }
          if ($headers[$key] == 'hourly_rate') {
            if ($field <= 0) {
              //$field = $daily_wage;
              $field = isset($daily_wage) ? $daily_wage : '-';
            }
          }
          if ($headers[$key] == 'annual_salary' ){
            if ($field <= 0) {
              $field = '-';
            }
          }
          break;
        case "edc_contracts":
          if ($headers[$key] == 'contract_entity_contract_number') {
            // Add space between the number to display the comma correctly in excel
            $field = str_replace(",", " , ", $field); ;
          }
          break;

      }

      if(isset($hyphen_fields) && in_array($headers[$key], $hyphen_fields)) {
        if ($field == null) {
          $field = '-';
        }
      }
      if(isset($category_hyphen) && in_array($headers[$key], $category_hyphen)) {
        if ($field == null) {
          $field = '-';
        }
      }
      if(isset($date_fields) && in_array($headers[$key], $date_fields)) {
        $field = '-';
      }
      if(isset($amount_hyphen_fields) && in_array($headers[$key], $amount_hyphen_fields)) {
        if ($field == null || $field <= 0 ) {
          $field = '-';
        }
      }
      $row[$headers[$key]] = $field;
    }
    if($domain == "citywide_payroll") {
      unset($row['daily_wage']);
    }
    // Write processed results to output
    //@fputcsv($output, $row);

    if (count($row) <= 1) {
      continue;
    }
    $return .= implode(',',$row) . PHP_EOL;
  }
  //fclose($output);
  return $return;
}

/** Exports the smart search export data
 * @param string $solr_datasource
 * @return Response
 */
function _checkbook_smart_search_export_data(string $solr_datasource) {
  $remaining = \Drupal::config('check_book')->get('smart_search')['export_record_limit'] ?? 200000;
  $page_size = \Drupal::config('check_book')->get('smart_search')['export_page_size'] ?? 10000;
  $page_num = 0;

  // Loads naming used in the export file.
  $request_domain = \Drupal::request()->query->get('domain');
  $domain = $solr_datasource . '_' . $request_domain;

  $fields = (array)CheckbookSolr::getExportFields($solr_datasource, \Drupal::request()->query->get('domain'));
  $fl = implode(",", array_keys($fields));
  // Remove daily wage display for citywide payroll
  if($domain == "citywide_payroll") {
    unset($fields['daily_wage']);
  }
  // Write header content to output file
  $output = implode(",", array_values($fields)) . PHP_EOL;
  $line_count ='0';
  $search_terms ='';
  while ($remaining > 0) {
    // Since SOLR 7 (gptext) doesn't seem to pick up "&csv.header=false" in _checkbook_smart_search_get_results_csv(), unset CSV header manually
    $search_terms = \Drupal::request()->query->get('search_terms') ? \Drupal::request()->query->get('search_terms') : '';
    $result = _checkbook_smart_search_get_results_csv(
      $solr_datasource,
      $search_terms,
      $page_num,
      $page_size,
      $fl,
      true
    );

    $page_num += 1;
    $remaining = $remaining - $page_size;

    //    last page reached => exit
    if (!$result) {
      $remaining = 0;
      continue;
    }

    // Process records before display
    $output .= adjustExportResults($result,$domain);
    $line_count++;
  }

  // Define the return file name.
  $filename = $solr_datasource.ucfirst($request_domain).".csv";

  $file_headers = array(
    'Content-Type' => 'text/csv',
    'Content-Disposition' => "attachment; filename=$filename",
    'Pragma' => 'cache',
    'Expires' => '-1',
  );
  $response = new Response($output, 200, $file_headers, true);
  return $response;
}

/**
 * @param string $solr_datasource
 * @param string $facet
 * @return array
 */
function _checkbook_autocomplete(string $solr_datasource, string $facet){

  $solr_query = new CheckbookSolrQuery($solr_datasource, \Drupal::request()->query->get('search_term') ?? '');

  $solr_query->setFqAutocompleteTerm($facet, \Drupal::request()->query->get('term') ?? '');

  $solr = new CheckbookSolr($solr_datasource);

  $results = $solr->request_phps('select/?' . $solr_query);

  if ($results['response']['numFound'] != 0 && ($results['facet_counts']['facet_fields'] ?? false)) {
    $facet_results = array_shift($results['facet_counts']['facet_fields']);
    //NYCCHKBK-9957 - The autocomplete displayed facets that were already selected. Hence, remove such facets.
    $selected_facets = $solr_query->getSelectedFacets()[$facet];
    $selected_facets_object = [];
    foreach($selected_facets as $key => $val) $selected_facets_object[$val] = $val;
    $facet_results = array_filter($facet_results, function($k) use ($selected_facets_object) {
                    return !isset($selected_facets_object[$k]);
                  }, ARRAY_FILTER_USE_KEY);

    $matches = [];
    foreach (array_keys($facet_results) as $key) {
      array_push($matches, ["url" => "", "category" => $facet, "label" => $key, "value" => $key]);
    }
    if(count($matches)>0) return $matches;
  }

  return [['label' => 'No Matches Found', 'value' => '']];
}

/**
 * @param $results
 * @return array
 */
function _checkbook_vendor_type_prepare_labels($results){
  $pv = $results['p'] + $results['pm'];
  $sv = $results['s'] + $results['sm'];
  $mv = $results['sm'] + $results['pm'];
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

/** Returns the URL for clear functionality for each Search Term
 * @param $facetName
 * @param null $facetValue
 * @param null $count
 * @return string
 */
function _checkbook_smart_search_clear_url(string $solr_datasource,$facetName, $facetValue = null, $count = null){

  $searchArgs = \Drupal::request()->query->get('search_term');
  $clearArgURL = "/smart_search/".$solr_datasource."?search_term=";
  $clearURL = "";
  $searchTerms = explode('*!*', $searchArgs);
  if ($facetName == 'search_term') {
    for ($i = 1; $i < count($searchTerms); $i++) {
      $search_params = explode('=', $searchTerms[$i]);
      //$search_params[1] = urldecode($search_params[1]);
      $clearURL .= "*!*" . $search_params[0] . '=' . urlencode($search_params[1]);
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
            $clearArgURL .= '*!*' . $facets[0] . '=' . urlencode($facets[1]);
          }

        } else if ($facetName == 'domains' && $facetValue == 'spending') {
          if ($facets[0] == 'spending_categories') {
            $clearArgURL .= "";
          } else {
            $clearArgURL .= '*!*' . $facets[0] . '=' . urlencode($facets[1]);
          }
        } else {
          $clearArgURL .= '*!*' . $facets[0] . '=' . urlencode($facets[1]);
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
        $clearArgURL .= ($count > 1) ? ('*!*' . $facetName . '=' . $contQueryString) : "";
      }
    }
  }

  return $clearArgURL;
}

/** Returns the html entities for string without replacing <em> and </em> tags.
 * @param $string
 * @return mixed|string $string.
 */
function _checkbook_smart_search_str_html_entities($string){
  $string = str_replace("<em>", "**!**", $string);
  $string = str_replace("</em>", "**||**", $string);
  $string = htmlentities($string);
  $string = str_replace("**!**", "<em>", $string);
  $string = str_replace("**||**", "</em>", $string);

  return $string;
}
