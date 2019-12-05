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

$searchTerms = explode('*!*', $_REQUEST['search_term']);
//$filterCriteria = NULL;
$theme_path =  drupal_get_path('theme',$GLOBALS['theme']);
$clear_icon = '/'.$theme_path."/images/filter-close-icon.png";

//Begin of displaying filter criteria

$clearUrl = _checkbook_smart_search_clear_url("search_term");
$searchTerm = urldecode($searchTerms[0]);

if($searchTerm != ""){
    print "<div class='search-filters'><span id='filter-header'> Filters: </span><ul>";
    print "<li><span class='search-terms'>Search Term: <strong>". htmlentities($searchTerm) ."</strong></span><a class='clear-filter' href='". $clearUrl ."'>
            <img src='".$clear_icon."'></a></li>";
    print "<li class='clear-all'><a class='clear-all' href='/smart_search/{$solr_datasource}'><strong>Clear All</strong></a></li>";
    print "</ul></div>";
}

//Begin of search results
$noOfTotalResults = $search_results['response']['numFound'];
$total = 5;
$noOfResultsPerPage = 10;
$startIndex = $transaction_no = ($_REQUEST['page'])? ($_REQUEST['page']*10)+1:1;
$endIndex = (($startIndex+9) < $noOfTotalResults)? ($startIndex+9) : $noOfTotalResults;
$domain_counts = $search_results['facet_counts']['facet_fields']['domain'];

if($noOfTotalResults > 0){

      //Begin of Facet results
    foreach($domain_counts as $key=>$value) {
        $noOfResults .= $noOfResults == '' ? $key.'|'.$value : '~'.$key.'|'.$value;
    }

    print "<div class='smart-search-right'>" . theme('smart_search_filter', [
        'solr_datasource' => $solr_datasource,
        'facets' => $search_results['facet_counts'],
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

        print theme('revenue', ['revenue_results'=> $value, 'searchTerm' => $searchTerms[0], 'solr_datasource' => $solr_datasource]);
        break;
      case "budget":
        print theme('budget', ['budget_results'=> $value, 'searchTerm' => $searchTerms[0], 'solr_datasource' => $solr_datasource]);
        break;
      case "spending":
        if($solr_datasource = Datasource::SOLR_NYCHA) {
          print theme('nycha_spending', ['spending_results' => $value, 'searchTerm' => $searchTerms[0], 'solr_datasource' => $solr_datasource]);
        }else{
          print theme('spending', ['spending_results' => $value, 'searchTerm' => $searchTerms[0], 'solr_datasource' => $solr_datasource]);
        }

        break;
      case "payroll":
        print theme('payroll', ['payroll_results'=> $value, 'searchTerm' => $searchTerms[0], 'solr_datasource' => $solr_datasource]);
        break;
      case "contracts":
        print theme('contracts', ['contracts_results'=> $value, 'searchTerm' => $searchTerms[0], 'solr_datasource' => $solr_datasource]);
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
