<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
?>
<?php
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
        $filterCriteria .= "<li><span class='search-terms'>Vendor: <strong>". $value ."</strong></span><a class='clear-filter' href='".$clearUrl."'>
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
/*if(count($searchTerms) == 1 && $searchTerm != ""){
  print "<div class='search-filters'><span id='filter-header'> Filters: </span><ul>";
  print "<li><span class='search-terms'>Search Term: <strong>". htmlentities($searchTerm) ."</strong></span><a class='clear-filter' href='". $clearUrl ."'>
            <img src='".$clear_icon."'></a></li>";
  print "<li class='clear-all'><a class='clear-all' href='/smart_search'><strong>Clear All</strong></a></li>";
  print "</ul></div>";
}
else if(count($searchTerms) > 1){
  print "<div class='search-filters'><span id='filter-header'> Filters: </span><ul>";
  if($searchTerm != ""){
    print "<li><span class='search-terms'>Search Term: <strong>". htmlentities($searchTerm) ."</strong></span><a class='clear-filter' href='".  $clearUrl ."'>
            <img src='".$clear_icon."'></a></li>";
  }

  print $filterCriteria;

  print "<li class='clear-all'><a class='clear-all' href='/smart_search'><strong>Clear All</strong></a></li>";
  print "</ul></div>";
}*/

//End of displaying filter criteria

//Begin of search results
$noOfTotalResults = $search_results['response']['numFound'];
$total = 5;
$noOfResultsPerPage = 10;
$startIndex = $transaction_no = ($_REQUEST['page'])? ($_REQUEST['page']*10)+1:1;
$endIndex = (($startIndex+9) < $noOfTotalResults)? ($startIndex+9) : $noOfTotalResults;

if($noOfTotalResults > 0){

      //Begin of Facet results

    print "<div class='smart-search-right'>". theme('smart_search_filter', array('facets'=> $facet_results,
      'active_contracts'=>$active_contracts, 'selected_facet_results' => $selected_facet_results));

    //End of Facet results

    //print "</td></tr></table>";
    print "</div>";
  //print "<table class='SmartSearchResults'><tr><td>";
  print "<div class='smart-search-left'>";
  print '<span class="exportSmartSearch" value="' . $noOfTotalResults. '" >Export</span>';
  //Begin of Pagination at the top
  pager_default_initialize($noOfTotalResults, $noOfResultsPerPage);
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

  print "<ol class='search-results'>";

  foreach($search_results['response']['docs'] as $key=>$value){
    print "<li>";
    print "<h3 class='title'>Transaction #". $transaction_no .": ". $value["domain"] ."</h3>";
    $transaction_no++;
    switch(strtolower($value["domain"])){
      case "revenue":
        print theme('revenue', array('revenue_results'=> $value, 'highlighting' => $search_results['highlighting']));
        break;
      case "budget":
        print theme('budget', array('budget_results'=> $value, 'highlighting' => $search_results['highlighting']));
        break;
      case "spending":
        print theme('spending', array('spending_results'=> $value, 'highlighting' => $search_results['highlighting']));
        break;
      case "payroll":
        print theme('payroll', array('payroll_results'=> $value, 'highlighting' => $search_results['highlighting']));
        break;
      case "contracts":
        print theme('contracts', array('contracts_results'=> $value, 'highlighting' => $search_results['highlighting']));
        break;
    }
    print "</li>";
  }
  print "</ol>";

  print "<div id='smart-search-transactions'>Showing: " . number_format($startIndex) . " to ". number_format($endIndex) ." of ". number_format($noOfTotalResults) ." entries</div>";
  //Begin of Pagination at the bottom

  pager_default_initialize($noOfTotalResults, $noOfResultsPerPage);
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

  //End of Pagination at the bottom

  //print "</td>";
  print "</div>";
  // End of search results
}
else{
  print "<strong>There are no search results found with this search criteria.</strong>";
}