<?php
/**
* This file is part of the Checkbook NYC financial transparency software.
* 
* Copyright (C) 2012, 2013 New York City
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

$searchTerms = explode('*|*', $_REQUEST['search_term']);
$filterCriteria = NULL;
$theme_path =  drupal_get_path('theme',$GLOBALS['theme']);
$clear_icon = $theme_path."/images/filter-close-icon.png";

//arrays for the selected facet values from the URL
for($i=1;$i < count($searchTerms);$i++){
  $filters = explode('=', $searchTerms[$i]);
  $filters[1] = urldecode($filters[1]);
  switch($filters[0]){
    case 'agency_names':
      $reqAgencies = explode("~", $filters[1]);
      foreach($reqAgencies as $key=>$value){
        $clearUrl = _checkbook_smart_search_clear_url($filters[0],$value);
        $filterCriteria .= "<li><span class='search-terms'>Agency: <strong>". htmlentities($value) ."</strong></span><a class='clear-filter' href='".$clearUrl."'>
                                <img src='".$clear_icon."'></a></li>";
      }
      break;
    case 'fiscal_years':
      $reqFiscalYears = explode("~", $filters[1]);
      foreach($reqFiscalYears as $key=>$value){
        $clearUrl = _checkbook_smart_search_clear_url($filters[0],$value);
        $filterCriteria .= "<li><span class='search-terms'>Fiscal Year: <strong>". $value ."</strong></span><a class='clear-filter' href='".$clearUrl."'>
                                <img src='".$clear_icon."'></a></li>";
      }
      break;
      case 'registered_fiscal_years':
          $regreqFiscalYears = explode("~", $filters[1]);
          foreach($regreqFiscalYears as $key=>$value){
              $clearUrl = _checkbook_smart_search_clear_url($filters[0],$value);
              $filterCriteria .= "<li><span class='search-terms'>Fiscal Year: <strong>". $value ."</strong></span><a class='clear-filter' href='".$clearUrl."'>
                                <img src='".$clear_icon."'></a></li>";
          }
          break;
    case 'domains':
      $reqDomains = explode("~", $filters[1]);
      foreach($reqDomains as $key=>$value){
        $clearUrl = _checkbook_smart_search_clear_url($filters[0],$value);
        $filterCriteria .= "<li><span class='search-terms'>Type of Data: <strong>". $value ."</strong></span><a class='clear-filter' href='".$clearUrl."'>
                             <img src='".$clear_icon."'></a></li>";
      }
      break;
    case 'contract_categories':
      $reqContractCategories = explode("~", $filters[1]);
      if(in_array('contracts', $reqDomains)){
        foreach($reqContractCategories as $key=>$value){
          $clearUrl = _checkbook_smart_search_clear_url($filters[0],$value,count($reqContractCategories));
          $filterCriteria .= "<li><span class='search-terms'>Contract Category: <strong>". $value ."</strong></span><a class='clear-filter' href='".$clearUrl."'>
                                <img src='".$clear_icon."'></a></li>";
        }
      }
      break;
    case 'contract_status':
      $reqContractStatus = explode("~", $filters[1]);
      if(in_array('contracts', $reqDomains)){
        foreach($reqContractStatus as $key=>$value){
          $clearUrl = _checkbook_smart_search_clear_url($filters[0],$value,count($reqContractStatus));
          $filterCriteria .= "<li><span class='search-terms'>Contract Status: <strong>". $value ."</strong></span><a class='clear-filter' href='".$clearUrl."'>
                                <img src='".$clear_icon."'></a></li>";
        }
      }
      break;
    case 'spending_categories':
      $reqSpendingCategories = explode("~", $filters[1]);
      if(in_array('spending', $reqDomains)){
        foreach($reqSpendingCategories as $key=>$value){
          $clearUrl = _checkbook_smart_search_clear_url($filters[0],$value,count($reqSpendingCategories));
          $filterCriteria .= "<li><span class='search-terms'>Spending Category: <strong>". $value ."</strong></span><a class='clear-filter' href='".$clearUrl."'>
                                <img src='".$theme_path."/filter-close-icon.png'></a></li>";
        }
      }
      break;
    case 'vendor_names':
      $reqVendors = explode("~", $filters[1]);
      foreach($reqVendors as $key=>$value){
        $clearUrl = _checkbook_smart_search_clear_url($filters[0],$value);
        $filterCriteria .= "<li><span class='search-terms'>Vendor: <strong>". 'kkkhk8 llalalala'."</strong></span><a class='clear-filter' href='".$clearUrl."'>
                            <img src='".$clear_icon."'></a></li>";
      }
      break;
    case 'expense_categories':
      $reqExpenseCategories = explode("~", $filters[1]);
      foreach($reqExpenseCategories as $key=>$value){
        $clearUrl = _checkbook_smart_search_clear_url($filters[0],$value);
        $filterCriteria .= "<li><span class='search-terms'>Expense Category: <strong>". $value ."</strong></span><a class='clear-filter' href='".$clearUrl."'>
                            <img src='".$clear_icon."'></a></li>";
      }
      break;
    case 'revenue_categories':
      $reqRevenueCategories = explode("~", $filters[1]);
      foreach($reqRevenueCategories as $key=>$value){
        $clearUrl = _checkbook_smart_search_clear_url($filters[0],$value);
        $filterCriteria .= "<li><span class='search-terms'>Revenue Category: <strong>". $value ."</strong></span><a class='clear-filter' href='".$clearUrl."'>
                            <img src='".$clear_icon."'></a></li>";
      }
      break;
  }
}

//Begin of displaying filter criteria

$clearUrl = _checkbook_smart_search_clear_url("search_term");
$searchTerm = urldecode($searchTerms[0]);

if($searchTerm != ""){
    print "<div class='search-filters'><span id='filter-header'> Filters: </span><ul>";
    print "<li><span class='search-terms'>Search Term: <strong>". htmlentities($searchTerm) ."</strong></span><a class='clear-filter' href='". $clearUrl ."'>
            <img src='".$clear_icon."'></a></li>";
    print "<li class='clear-all'><a class='clear-all' href='/smart_search'><strong>Clear All</strong></a></li>";
    print "</ul></div>";
}

//Begin of search results
$noOfTotalResults = $search_results['response']['numFound'];
$total = 5;
$noOfResultsPerPage = 10;
$startIndex = $transaction_no = ($_REQUEST['page'])? ($_REQUEST['page']*10)+1:1;
$endIndex = (($startIndex+9) < $noOfTotalResults)? ($startIndex+9) : $noOfTotalResults;
$domain_counts = $facet_results['domain'];

if($noOfTotalResults > 0){

      //Begin of Facet results
    foreach($domain_counts as $key=>$value) {
        $noOfResults .= $noOfResults == '' ? $key.'|'.$value : '~'.$key.'|'.$value;
    }

    print "<div class='smart-search-right'>" . theme('smart_search_filter', [
        'solr_datasource' => $solr_datasource,
        'facets' => $facet_results,
        'active_contracts' => $active_contracts,
        'registered_contracts' => $registered_contracts,
        'selected_facet_results' => $selected_facet_results
      ]);

    //End of Facet results

  print "</div>";
  print '<span class="exportSmartSearch" value="' . $noOfResults. '" >Export</span>';
  print "<div class='smart-search-left'>";
  //Begin of Pagination at the top
    if($noOfTotalResults > 1000000){
        pager_default_initialize(1000000, $noOfResultsPerPage);
    }else{
        pager_default_initialize($noOfTotalResults, $noOfResultsPerPage);
    }

  $output = theme('pager', array('quantity' => $total));
  if($output==""){
   $output= '<div class="item-list"><ul class="pager">
   <li class="pager-first first"><a href="" title="Go to first page" class="pagerItemDisabled">First</a></li>
   <li class="pager-first previous"><a href="" title="Go to previous page" class="pagerItemDisabled">Previous</a></li>
   <li class="pager-current">1</li>
   <li class="pager-first next"><a href="" title="Go to Next page" class="pagerItemDisabled">Next</a></li>
   <li class="pager-first last"><a href="" title="Go to Last page" class="pagerItemDisabled">Last</a></li>
   </ul></div>'; 
  }
  print $output;

  
  
  //End of Pagination at the top
  print "<div class='loading' style='display:none;'></div>";
  print "<ol class='search-results'>";

  foreach($search_results['response']['docs'] as $key=>$value){
    print "<li>";
    $domain_display = $value["domain"];
    if($domain_display == "budget"){
      $domain_display = "Expense Budget";
    }
    print "<h3 class='title'>Transaction #". $transaction_no .": ". $domain_display ."</h3>";
    $transaction_no++;
    switch(strtolower($value["domain"])){
      case "revenue":

        print theme('revenue', array('revenue_results'=> $value, 'searchTerm' => $searchTerms[0]));
        break;
      case "budget":
        print theme('budget', array('budget_results'=> $value, 'searchTerm' => $searchTerms[0]));
        break;
      case "spending":
        print theme('spending', array('spending_results'=> $value, 'searchTerm' => $searchTerms[0], 'IsOge' => isset($value["oge_agency_name"]) ));
        break;
      case "payroll":
        print theme('payroll', array('payroll_results'=> $value, 'searchTerm' => $searchTerms[0], 'IsOge' => isset($value["oge_agency_name"])));
        break;
      case "contracts":
        print theme('contracts', array('contracts_results'=> $value, 'searchTerm' => $searchTerms[0], 'IsOge' => isset($value["oge_agency_name"]) ));
        break;
    }
    print "</li>";
  }
  print "</ol>";

  print "<div id='smart-search-transactions'>Showing: " . number_format($startIndex) . " to ". number_format($endIndex) ." of ". number_format($noOfTotalResults) ." entries</div>";
  //Begin of Pagination at the bottom
    if($noOfTotalResults > 1000000){
        pager_default_initialize(1000000, $noOfResultsPerPage);
    }else{
        pager_default_initialize($noOfTotalResults, $noOfResultsPerPage);
    }
  $output = theme('pager', array('quantity' => $total));
  if($output==""){
    $output= '<div class=" item-list"><ul class="pager">
    <li class="pager-first first"><a href="" title="Go to first page" class="pagerItemDisabled">First</a></li>
    <li class="pager-first previous"><a href="" title="Go to previous page" class="pagerItemDisabled">Previous</a></li>
    <li class="pager-current">1</li>
    <li class="pager-first next"><a href="" title="Go to Next page" class="pagerItemDisabled">Next</a></li>
    <li class="pager-first last"><a href="" title="Go to Last page" class="pagerItemDisabled">Last</a></li>
    </ul></div>';
  }
  print $output;

  //End of Pagination at the bottom

  print "</div>";
  // End of search results
}
else{
  print "<strong>There are no search results found with this search criteria.</strong>";
}
