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

namespace Drupal\checkbook_smart_search\Twig;

use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;
use Drupal\checkbook_project\CommonUtilities\CheckbookDateUtil;
use Drupal\checkbook_smart_search\Util\BudgetSmartUtil;
use Drupal\checkbook_smart_search\Util\ContractsSmartUtil;
use Drupal\checkbook_smart_search\Util\NychaBudgetSmartUtil;
use Drupal\checkbook_smart_search\Util\NychaContractsSmartUtil;
use Drupal\checkbook_smart_search\Util\NychaRevenueSmartUtil;
use Drupal\checkbook_smart_search\Util\NychaSpendingSmartUtil;
use Drupal\checkbook_smart_search\Util\PayrollSmartUtil;
use Drupal\checkbook_smart_search\Util\RevenueSmartUtil;
use Drupal\checkbook_smart_search\Util\SpendingSmartUtil;
use Drupal\Component\Utility\Html;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SmartSearchExtension extends AbstractExtension {

  /**
   * Generates a list of all Twig functions that this extension defines.
   *
   * @return array
   *   A key/value array that defines custom Twig functions. The key denotes the
   *   function name used in the tag, e.g.:
   * @code
   *   {{ testfunc() }}
   * @endcode
   *
   *   The value is a standard PHP callback that defines what the function does.
   */
  public function getFunctions() {
    return [
      'smartSearchResults' => new TwigFunction('smartSearchResults', [$this, 'smartSearchResults',]),
      'getDomainResults' => new TwigFunction('getDomainResults', [$this, 'getDomainResults',]),
      'runExportDialog' => new TwigFunction('runExportDialog', [$this, 'runExportDialog',]),
      'FacetData' => new TwigFunction('FacetData', [$this, 'FacetData',]),
      'displayFacetData' => new TwigFunction('displayFacetData', [$this, 'displayFacetData',]),
      'displayAjaxResults' => new TwigFunction('displayAjaxResults', [$this, 'displayAjaxResults',])
    ];
  }

  public function smartSearchResults($search_results, $solr_datasource) {
    $noOfResults = '';
    $output = '';
    $searchTerms = explode('*!*', \Drupal::request()->query->get('search_term'));
    $theme_path = \Drupal::service('extension.list.theme')->getPath(\Drupal::theme()->getActiveTheme()->getName());
    $clear_icon = '/' . $theme_path . "/images/filter-close-icon.png";

    //Begin of displaying filter criteria
    $clearUrl = _checkbook_smart_search_clear_url($solr_datasource, "search_term");
    $searchTerm = urldecode($searchTerms[0]);

    if ($searchTerm != "") {
      print "<div class='search-filters clearfix'><span id='filter-header'> Filters: </span><ul>";
      print "<li><span class='search-terms'>Search Term: <strong>" . htmlentities($searchTerm) . "</strong></span><a class='clear-filter' href='" . $clearUrl . "'>
            <img src='" . $clear_icon . "'></a></li>";
      print "<li class='clear-all'><a class='clear-all' href='/smart_search/{$solr_datasource}'><strong>Clear All</strong></a></li>";
      print "</ul></div>";
    }

    //Begin of search results
    $noOfTotalResults = $search_results['response']['numFound'];
    $noOfResultsPerPage = 10;
    $startIndex = $transaction_no = (\Drupal::request()->query->get('page')) ? (\Drupal::request()->query->get('page') * 10) + 1 : 1;
    $endIndex = (($startIndex + 9) < $noOfTotalResults) ? ($startIndex + 9) : $noOfTotalResults;
    $domain_counts = $search_results['facet_counts']['facet_fields']['domain'];

    if($noOfTotalResults > 0) {
    foreach ($domain_counts as $key => $value) {
      $noOfResults .= $noOfResults == '' ? $key . '|' . $value : '~' . $key . '|' . $value;
    }
    print "<span class='export exportSmartSearch' value=" . $noOfResults . ">export</span>";
    print "<div class=\"smart-search-left\">";
    //Begin of Pagination at the top
    $pager_manager = \Drupal::service('pager.manager');
    if ($noOfTotalResults > 1000000){
      $pager_manager->createPager(1000000, $noOfResultsPerPage);
    }
    else {
      $pager_manager->createPager($noOfTotalResults, $noOfResultsPerPage);
    }
    $pager = [
      '#type' => 'pager',
      '#quantity' => 5
    ];
    print \Drupal::service('renderer')->render($pager);
    //End of Pagination at the top

    print "<div class='loading' style='display:none;'></div>";
    print "<ol class='search-results add-list-reset'>";
    foreach ($search_results['response']['docs'] as $key => $value) {
      $output .= "<li>";
      $domain_display = $value['domain'];
      if ($domain_display == "budget") {
        $domain_display = "Expense Budget";
      }
      $output .= "<h3 class='title'>Transaction #" . $transaction_no . ": " . $domain_display . "</h3>";
      $transaction_no++;
      switch (strtolower($value['domain'])) {
        case 'budget':
          $template = ($solr_datasource == Datasource::SOLR_NYCHA) ? "nycha_budget" : "budget";
          break;
        case 'contracts':
          $template = ($solr_datasource == Datasource::SOLR_NYCHA) ? "nycha_contracts" : "contracts";
          break;
        case 'payroll':
          $template = 'payroll';
          break;
        case 'revenue':
          $template = ($solr_datasource == Datasource::SOLR_NYCHA) ? "nycha_revenue" : "revenue";
          break;
        case 'spending':
          $template = ($solr_datasource == Datasource::SOLR_NYCHA) ? "nycha_spending" : "spending";
          break;
      }
      $results = checkbook_smart_search_display_domain_results($value, $solr_datasource, $template);
      $output .= $results;
      $output .= "</li>";
    }
    $output .= "</ol>";
    $output .= "<div id=\"smart-search-transactions\">Showing: " . number_format($startIndex) . " to ". number_format($endIndex) ." of ". number_format($noOfTotalResults) ." entries</div>";
    echo $output;
      $pager_manager = \Drupal::service('pager.manager');
      if ($noOfTotalResults > 1000000){
        $pager_manager->createPager(1000000, $noOfResultsPerPage);
      }
      else {
        $pager_manager->createPager($noOfTotalResults, $noOfResultsPerPage);
      }
      $pager = [
        '#type' => 'pager',
        '#quantity' => 5
      ];
    print \Drupal::service('renderer')->render($pager);
    print "</div>";
  }
    else {
      print "<strong>There are no search results found with this search criteria.</strong>";
    }
  }

  public function getDomainResults($results, $solr_datasource, $domain) {
    switch ($domain) {
      case "budget":
        $final_result = BudgetSmartUtil::displayBudgetResult($results, $solr_datasource);
        break;
      case "nycha_budget":
        $final_result = NychaBudgetSmartUtil::displayNychaBudgetResult($results, $solr_datasource);
        break;
      case "contracts":
        $final_result = ContractsSmartUtil::displayContractsResult($results, $solr_datasource);
        break;
      case "nycha_contracts":
        $final_result = NychaContractsSmartUtil::displayNychaContractsResult($results, $solr_datasource);
        break;
      case "payroll":
        $final_result = PayrollSmartUtil::displayPayrollResult($results, $solr_datasource);
        break;
      case "revenue":
        $final_result = RevenueSmartUtil::displayRevenueResult($results, $solr_datasource);
        break;
      case "nycha_revenue":
        $final_result = NychaRevenueSmartUtil::displayNychaRevenueResult($results, $solr_datasource);
        break;
      case "spending":
        $final_result = SpendingSmartUtil::displaySpendingResult($results, $solr_datasource);
        break;
      case "nycha_spending":
        $final_result = NychaSpendingSmartUtil::displayNychaSpendingResult($results, $solr_datasource);
        break;
    }
    return $final_result;
  }

  public function runExportDialog() {
    $max_records = 200000;
    $search_terms = explode('*!*', \Drupal::request()->query->get('searchTerm'));
    $domains = explode("~", \Drupal::request()->query->get('resultsdomains'));
    $domain_record_counts = explode("~", Html::escape(\Drupal::request()->query->get('totalRecords')));

    $all_domains = FALSE;
    if (count($domains) == 0) {
      $checked = "spending";
      $all_domains = TRUE;
    }
    else {
      if (in_array("spending", $domains)) {
        $checked = "spending";
      }
      elseif (in_array("payroll", $domains)) {
        $checked = "payroll";
      }
      elseif (in_array("contracts", $domains)) {
        $checked = "contracts";
      }
      elseif (in_array("budget", $domains)) {
        $checked = "budget";
      }
      elseif (in_array("revenue", $domains)) {
        $checked = "revenue";
      }
    }

    $total_records = $domain_records = 0;
    foreach ($domain_record_counts as $domain_record_count) {
      $domain_count = explode("|", $domain_record_count);
      if ($checked == $domain_count[0]) {
        $domain_records = $domain_count[1];
      }
      $total_records .= $domain_count[1];
    }

  }

  /**
   * Functions for facets display and processing starts here
   *
   */
  //Call the function to generate twig based on the input parameters
  public function FacetData($facet_counts,$solr_datasource,$selected_facet_results,$registered_contracts,$active_contracts) {
    if(is_countable($facet_counts) && !empty(array_filter($facet_counts))) {
      $facetMarkup = checkbook_smart_search_facet_results($facet_counts, $solr_datasource, $selected_facet_results, $registered_contracts, $active_contracts);
      return '<div class="smart-search-right">'. $facetMarkup . '</div>';
    }else{
      return '';
    }
  }

  public function displayFacetData($facets_render,$solr_datasource,$selected_facet_results,$registered_contracts,$active_contracts) {

    if(!isset($selected_facet_results)){
      $selected_facet_results=[];
    }
    $output = '';
    $no_of_selected_options = '0';

    //ticket NYCCHKBK-13156 - moving below line out of inner for loop (line 334) - foe slowness issue
    $current_year = CheckbookDateUtil::getCurrentDatasourceFiscalYear(Datasource::getCurrent());

    foreach ($facets_render??[] as $facet_name => $facet) {
      // skipping children (sub facets)
      if ($facet->child??false){
        continue;
      }
      $span='';
      $display_facet = 'none';

      // keep domain facet always open
      if (isset($facet->selected) || in_array($facet_name,['domain'])) {
        $span = 'open';
        $display_facet = 'block';
      }

      // If contracts selected, use register to determine facet_year display.
      if(!empty($selected_facet_results['contract_status'])){

        if(in_array('registered', $selected_facet_results['contract_status'])
          && strtolower($facet_name) == 'facet_year_array'){
          continue;
        }
        if(!in_array('registered', $selected_facet_results['contract_status'])
          && strtolower($facet_name) == 'registered_fiscal_year'){
          continue;
        }

      } else {
        // If contract is not selected, hide registered_fiscal_year..
        if( strtolower($facet_name) == 'registered_fiscal_year'){
          continue;
        }

        // Hide contract prime vendor..
        if( strtolower($facet_name) == 'contract_prime_vendor_name'){
          continue;
        }

      }

      $output .= '<div class="filter-content-' . $facet_name . ' filter-content">'.
                 '<div class="filter-title"><span class="'.$span.'">By ' . htmlentities($facet->title) . '</span></div>'.
                 '<div class="facet-content" style="display:'.$display_facet.'" ><div class="progress"></div>';

      if (isset($facet->autocomplete) && isset($facet->results) && sizeof($facet->results)>9) {
        $autocomplete_id = "autocomplete_" . $facet_name;
        $disabled = '';

        // Autocomplete's result(s) displays and allows to select options that are already selected
        // thereby counting an option twice. Hence, removing duplicates
        // The below threw: Undefined property: stdClass::$selected
        // $facet->selected  = array_unique($facet->selected? $facet->selected:[]);
        if(isset($facet->selected)) {
          $facet->selected = array_unique($facet->selected);
        } else {
          $facet->selected = [];
        };

        //NYCCHKBK-9957 : Disable autocomplete search box if 5 or more options are selected
        $no_of_selected_options = count($facet->selected ? $facet->selected: []);
        if($no_of_selected_options >= 5) $disabled = " DISABLED=true";

        $output .= '<div class="autocomplete">
              <input id="' . $autocomplete_id . '" ' . $disabled . ' type="text" class="solr_autocomplete" facet="'.$facet_name.'" />
            </div>';
      }
      $output .= '<div class="options">';
      $output .= '<ul class="add-list-reset rows">';
      $index = 0;
      $lowercase_selected = [];
      if(!empty($facet->selected)){
        foreach($facet->selected as $facet_val){
          $lowercase_selected[strtolower($facet_val)] = strtolower($facet_val);
        }
      }

      /* Populate the facet checkboxes. */
      foreach($facet->results as $facet_value => $count) {

        if ($facet_name == 'facet_year_array') {

          if ($facet_value > $current_year) {
            continue;
          }
        }
        $facet_result_title = $facet_value;
        if (is_array($count)) {
          [$facet_result_title, $count] = $count;
        }

        $id = 'facet_'.$facet_name.$index;
        $active='';
        $output .= '<li class="row">
               <label for="'.$id.'">
             <div class="checkbox">';
        if ($lowercase_selected && isset($lowercase_selected[strtolower($facet_value)])) {
          $checked = ' checked="checked" ';
          $active = ' class="active" ';
          $disabled = '';
        }
        else{
          $checked = '';
          $active = '';
          $disabled = '';
        }

        //Disable unchecked options if 5 or more options from the same category are already selected
        if((!$checked || $checked == '')  && (count($lowercase_selected) >= 5)){
          $disabled = " DISABLED=true";
        }

        $output .= '<input type="checkbox" onclick="return applySearchFilters();" id="'.$id.'" '.$checked . $disabled.' facet="'.$facet_name.'" value="'.
          htmlentities(urlencode($facet_value)).'" />';
        $output .= '<label for="'.$id.'" /></div>'
                  .'<div class="number"><span' . $active . '>' . number_format($count) . '</span></div>'
                  .'<div class="name">' . htmlentities($facet_result_title) . '</div>'
                  .'</label></li>';

        if (($checked) && ($children = $facet->sub->$facet_value??false)){
          $sub_index=0;
          foreach($children as $child){
            $sub_facet = $facets_render[$child];
            if (!$sub_facet) {
              continue;
            }

            $sub_facet_name = $child;
            $output .= '<ul class="sub-category add-list-reset">'
                    .'<div class="subcat-filter-title">By '.htmlentities($sub_facet->title).'</div>';
            //Set Active and Registered Contracts Counts
            if($sub_facet_name == 'contract_status'){
              unset($sub_facet->results['registered']);
              unset($sub_facet->results['active']);
              if($registered_contracts > 0 ) {
                $sub_facet->results['registered'] = $registered_contracts;
              }
              if($active_contracts > 0) {
                $sub_facet->results['active'] = $active_contracts;
              }
            }

            foreach($sub_facet->results as $sub_facet_value => $sub_count){
              $facet_result_title = $sub_facet_value;
              if (is_array($sub_count)) {
                [$facet_result_title, $sub_count] = $sub_count;
              }

              $id = 'facet_'.$sub_facet_name.$sub_index;
              $active='';
              $output .= '<li class="row">'
                      ."<label for=\"$id\">"
                      .'<div class="checkbox">';
              $checked = '';
              if (isset($sub_facet->selected) && is_array($sub_facet->selected)) {
                $checked = in_array($sub_facet_value, $sub_facet->selected);
              }

              $checked = $checked ? ' checked="checked" ' : '';
              $active = $checked ? ' class="active" ' : '';

              if(isset($sub_facet->input) && $sub_facet->input == 'radio'){
                $output .= '<input type="radio" name="' . htmlentities($sub_facet->title) . '" ' . 'id="' . $id . '" ' . $checked . ' facet="' . $sub_facet_name . '" value="' .
                  htmlentities(urlencode($sub_facet_value)) . '" />';
              }else{
                $output .= '<input type="checkbox" onclick="return applySearchFilters();" id="' . $id . '" ' . $checked . ' facet="' . $sub_facet_name . '" value="' .
                  htmlentities(urlencode($sub_facet_value)) . '" />';
              }
              $output .= "<label for=\"$id\" />"
                      .'</div>'
                      .'<div class="number"><span' . $active . '>' . number_format($sub_count) . '</span></div>'
                      .'<div class="name">' . htmlentities($facet_result_title) . '</div>'
                      .'</label>'
                      .'</li>';
              $sub_index++;
            }
            $output .= '</ul>';
          }
        }

        $index++;
      }
      $output .= '</ul></div></div></div>';
    }
    if(strlen($output) > 0){
      $output = '<div class="narrow-down-filter">
                    <div class="narrow-down-title">Narrow Down Your Search:</div>'
                    . $output .
                '</div>';
    }
    return $output;
  }

  public function displayAjaxResults($solr_datasource, $search_results) {
    $searchTerms = explode('*!*', \Drupal::request()->query->get('search_term'));
    //Begin of search results
    $noOfTotalResults = $search_results['response']['numFound'];
    $noOfResultsPerPage = 10;
    $startIndex = $transaction_no = ( \Drupal::request()->query->get('page') ) ? ( \Drupal::request()->query->get('page') * 10) + 1 : 1;
    $endIndex = (($startIndex+9) < $noOfTotalResults)? ($startIndex+9) : $noOfTotalResults;

    if ($noOfTotalResults > 0) {
      //Begin of Pagination at the top
      $pager_manager = \Drupal::service('pager.manager');
      if ($noOfTotalResults > 1000000){
        $pager_manager->createPager(1000000, $noOfResultsPerPage);
      }
      else {
        $pager_manager->createPager($noOfTotalResults, $noOfResultsPerPage);
      }
      $pager = [
        '#type' => 'pager',
        '#quantity' => 5
      ];
      print \Drupal::service('renderer')->render($pager);
      //End of Pagination at the top

      print "<div class=\"loading\" style=\"display:none\"></div>";
      print "<ol class=\"search-results\">";

      foreach($search_results['response']['docs'] as $key=>$value){
        $output .= "<li>";
        $domain_display = $value['domain'];
        if ($domain_display == "budget"){
          $domain_display = "Expense Budget";
        }
        $output .= "<h3 class=\"title\">Transaction #". $transaction_no .": ". $domain_display ."</h3>";
        $transaction_no++;
        switch(strtolower($value['domain'])){
          case "revenue":
            $template = ($solr_datasource == Datasource::SOLR_NYCHA) ? "nycha_revenue" : "revenue";
            $results = 'revenue_results';
            break;
          case "budget":
            $template = ($solr_datasource == Datasource::SOLR_NYCHA) ? "nycha_budget" : "budget";
            $results = 'budget_results';
            break;
          case "spending":
            $template = ($solr_datasource == Datasource::SOLR_NYCHA) ? "nycha_spending" : "spending";
            $results = 'spending_results';
            break;
          case "payroll":
            $template = "payroll";
            $results = 'payroll_results';
            break;
          case "contracts":
            $template = ($solr_datasource == Datasource::SOLR_NYCHA) ? "nycha_contracts" : "contracts";
            $results = 'contracts_results';
            break;
        }
        $results = checkbook_smart_search_display_domain_results($value, $solr_datasource, $template);
        $output .= $results;
        $output .= "</li>";
      }
      $output .= "</ol>";
      $output .= "<div id=\"smart-search-transactions\">Showing: " . number_format($startIndex) . " to ". number_format($endIndex) ." of ". number_format($noOfTotalResults) ." entries</div>";
      echo $output;
      $pager_manager = \Drupal::service('pager.manager');
      if ($noOfTotalResults > 1000000){
        $pager_manager->createPager(1000000, $noOfResultsPerPage);
      }
      else {
        $pager_manager->createPager($noOfTotalResults, $noOfResultsPerPage);
      }
      $pager = [
        '#type' => 'pager',
        '#quantity' => 5
      ];
      print \Drupal::service('renderer')->render($pager);
    }
    else {
      print "<strong>There are no search results found with this search criteria.</strong>";
    }
  }

} // close class
