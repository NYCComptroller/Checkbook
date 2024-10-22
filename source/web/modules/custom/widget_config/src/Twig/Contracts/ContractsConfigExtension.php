<?php

namespace Drupal\widget_config\Twig\Contracts;

use Drupal\checkbook_custom_breadcrumbs\ContractsBreadcrumbs;
use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;
use Drupal\checkbook_infrastructure_layer\Utilities\FormattingUtilities;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_project\CommonUtilities\CheckbookDateUtil;
use Drupal\checkbook_project\CommonUtilities\RequestUtil;
use Drupal\checkbook_project\MwbeUtilities\MappingUtil;
use Drupal\checkbook_project\WidgetUtilities\NodeSummaryUtil;
use Drupal\checkbook_project\WidgetUtilities\WidgetUtil;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ContractsConfigExtension extends AbstractExtension
{
  public function getFunctions()
  {
    return [
      'ActiveRegisteredTitle' => new TwigFunction('ActiveRegisteredTitle', [
        $this,
        'ActiveRegisteredTitle',
      ]),
      'ActiveRegisteredDashboardTitle' => new TwigFunction('ActiveRegisteredDashboardTitle', [
        $this,
        'ActiveRegisteredDashboardTitle',
      ]),
      'ActiveRegisteredTotalAmount' => new TwigFunction('ActiveRegisteredTotalAmount', [
        $this,
        'ActiveRegisteredTotalAmount',
      ]),
      'expense' => new TwigFunction('expense', [
        $this,
        'expense',
      ]),
      'expenseContract' => new TwigFunction('expenseContract', [
        $this,
        'expenseContract',
      ]),
      'subContract' => new TwigFunction('subContract', [
        $this,
        'subContract',
      ]),
      'pendingContractVendorInfo' => new TwigFunction('pendingContractVendorInfo', [
        $this,
        'pendingContractVendorInfo',
      ]),
      'PendingTransactionTitle' => new TwigFunction('PendingTransactionTitle', [
        $this,
        'PendingTransactionTitle',
      ]),
      'getTransactionYearVal' => new TwigFunction('getTransactionYearVal', [
        $this,
        'getTransactionYearVal',
      ]),
      'isActiveExpenseContractsSubvendor' => new TwigFunction('isActiveExpenseContractsSubvendor', [
        $this,
        'isActiveExpenseContractsSubvendor',
      ]),
      'contractTitle' => new TwigFunction('contractTitle', [
        $this,
        'contractTitle',
      ]),
      'contractTopamoutGrid' => new TwigFunction('contractTopamoutGrid', [
        $this,
        'contractTopamoutGrid',
      ]),

    ];
  }
  public function ActiveRegisteredTitle($dashboard,$smnid,$mocsContracts)
  {
    $current_url = explode('/', request_uri());
    if($current_url[1] == 'contract' && ($current_url[2] == 'search' || $current_url[2] == 'all')&& $current_url[3] == 'transactions'){
      $summaryTitle = "";
      //For NYCEDC advanced search results
      $edcSubTitle = Datasource::isOGE() ? Datasource::EDC_TITLE . " " : "";
    }else if(!RequestUtilities::getTransactionsParams('mwbe') || $dashboard){
      $summaryTitle = RequestUtil::getDashboardTitle()." ";
    }
//Handle Sub Vendor widget to not repeat 'Sub Vendor' in title in certain dashboards
    $suppress_widget_title = ($dashboard == "ss" && $smnid == 720) || //Sub Vendors
      ($dashboard == "sp" && $smnid == 720); //Sub Vendors (M/WBE)
    if(!$suppress_widget_title) {
      $summaryTitle .= NodeSummaryUtil::getInitNodeSummaryTitle($smnid);
    }
    if ($mocsContracts == 'Yes'){
      $summaryTitle = 'MOCS Registered COVID-19 Contracts';
    }
    $summaryTitle = $summaryTitle != '' ? $summaryTitle : '';
    return array($summaryTitle, $edcSubTitle);
  }

  public function ActiveRegisteredDashboardTitle($dashboard,$smnid,$bottomNavigation)
  {
    if ($dashboard == 'ss' || $dashboard == 'sp') {
      switch ($smnid) {
        case '720':
        case '721':
          $title = "<h2 class='contract-title' class='title'>{$bottomNavigation} Transactions</h2>";
          $_checkbook_breadcrumb_title = "$bottomNavigation Transactions";
          break;
        case '722':
          $title = "<h2 class='contract-title' class='title'>Amount Modifications by {$bottomNavigation} Transactions</h2>";
          $_checkbook_breadcrumb_title = "Amount Modifications by $bottomNavigation Transactions";
          break;
        case '725':
          $title = "<h2 class='contract-title' class='title'>Prime Vendors with {$bottomNavigation} Transactions</h2>";
          $_checkbook_breadcrumb_title = "Prime Vendors with $bottomNavigation Transactions";
          break;
        case '726':
          $title = "<h2 class='contract-title' class='title'>Award Methods by {$bottomNavigation} Transactions</h2>";
          $_checkbook_breadcrumb_title = "Award Methods by $bottomNavigation Transactions";
          break;
        case '727':
          $title = "<h2 class='contract-title' class='title'>Agencies by {$bottomNavigation} Transactions</h2>";
          $_checkbook_breadcrumb_title = "Agencies by $bottomNavigation Transactions";
          break;
        case '728':
          $title = "<h2 class='contract-title' class='title'>Contracts by Industries by {$bottomNavigation} Transactions</h2>";
          $_checkbook_breadcrumb_title = "$bottomNavigation Transactions";
          break;
        case '729':
          $title = "<h2 class='contract-title' class='title'>Contracts by Size by {$bottomNavigation} Transactions</h2>";
          $_checkbook_breadcrumb_title = "Contracts by Size by $bottomNavigation Transactions";
          break;
      }
    }
    elseif ($dashboard == 'ms') {
      switch ($smnid) {
        case '781':
        case '784':
          $title = "<h2 class='contract-title' class='title'>{$bottomNavigation} Transactions</h2>";
          $_checkbook_breadcrumb_title = "$bottomNavigation Transactions";
          break;
        case '782':
          $title = "<h2 class='contract-title' class='title'>Amount Modifications by {$bottomNavigation} Transactions</h2>";
          $_checkbook_breadcrumb_title = "Amount Modifications by $bottomNavigation Transactions";
          break;
        case '783':
          $title = "<h2 class='contract-title' class='title'>Prime Vendors with {$bottomNavigation} Transactions</h2>";
          $_checkbook_breadcrumb_title = "Prime Vendors with $bottomNavigation Transactions";
          break;
        case '785':
          $title = "<h2 class='contract-title' class='title'>Award Methods by {$bottomNavigation} Transactions</h2>";
          $_checkbook_breadcrumb_title = "Award Methods by $bottomNavigation Transactions";
          break;
        case '786':
          $title = "<h2 class='contract-title' class='title'>Agencies by {$bottomNavigation} Transactions</h2>";
          $_checkbook_breadcrumb_title = "Agencies by $bottomNavigation Transactions";
          break;
        case '787':
          $title = "<h2 class='contract-title' class='title'>Contracts by Industries by {$bottomNavigation} Transactions</h2>";
          $_checkbook_breadcrumb_title = "$bottomNavigation Transactions";
          break;
        case '788':
          $title = "<h2 class='contract-title' class='title'>Contracts by Size by {$bottomNavigation} Transactions</h2>";
          $_checkbook_breadcrumb_title = "Contracts by Size by $bottomNavigation Transactions";
          break;
      }
    }
    return array($title, $_checkbook_breadcrumb_title);
  }

  public function ActiveRegisteredTotalAmount($node)
  {
    $http_ref = \Drupal::request()->server->get('HTTP_REFERER');
    $current_url = \Drupal::request()->query->get('q');

    //Advanced Search page should not have static text
    $advanced_search_page = preg_match("/contract\/search\/transactions/",$current_url);
    $advanced_search_page = $advanced_search_page || preg_match("/contract\/all\/transactions/",$current_url);
    $advanced_search_page = $advanced_search_page || preg_match("/contract\/search\/transactions/",$http_ref);
    $advanced_search_page = $advanced_search_page || preg_match("/contract\/all\/transactions/",$http_ref);
    if($advanced_search_page) return;

    $contactStatus = RequestUtilities::getTransactionsParams('contstatus');
    $contactStatusLabel = 'Active';
    if($contactStatus == 'R'){
      $contactStatusLabel = 'Registered';
    }
    if(Datasource::isOGE()){
      $output =  '<div class="transactions-total-amount">$'
        . FormattingUtilities::custom_number_formatter_format($node->data[0]['total_amount_for_transaction'],2)
        .'<div class="amount-title">Total '.$contactStatusLabel.' Current Contract Amount</div></div>';

    }else if(_checkbook_check_is_mwbe_page() || RequestUtilities::getTransactionsParams('dashboard')){
      $current_url = explode('/',request_uri());
      if($current_url[1] == 'contract' && ($current_url[2] == 'search' || $current_url[2] == 'all')&& $current_url[3] == 'transactions'){
        $summaryTitle = "";
      }else{
        $summaryTitle =  'Total '.RequestUtil::getDashboardTitle()." ";
        $summaryTitle = str_replace('Total Total','Total',$summaryTitle);
      }
      if($contactStatus == 'R'){
        $output = '<div class="transactions-total-amount">$'
          . FormattingUtilities::custom_number_formatter_format($node->data[0]['total_maximum_contract_amount'],2)
          .'<div class="amount-title">Total New Sub Vendor Current Contract Amount</div></div>';
      }else{
        $output ='<div class="transactions-total-amount">$'
          . FormattingUtilities::custom_number_formatter_format($node->data[0]['total_maximum_contract_amount'],2)
          .'<div class="amount-title">Total '.$contactStatusLabel.' Current Contract Amount</div></div>';
      }
    }else{
      $output = '<div class="transactions-total-amount">$'
        . FormattingUtilities::custom_number_formatter_format($node->data[0]['total_maximum_contract_amount'],2)
        .'<div class="amount-title">Total '.$contactStatusLabel.' Current Contract Amount</div></div>';
    }
  return $output;
  }

  public function expense($node)
  {
    $records = $node->data;
    if(is_array($records)){
      $row = $records[0];
      $noContr = WidgetUtil::getLabel("no_of_contracts");
      $smnid = RequestUtilities::getTransactionsParams('smnid');
      //$dynamicLabel = $node->widgetConfig->entityColumnLabel;
      $dynamicLabel = $node->widgetConfig->summaryView->entityColumnLabel ?? $node->widgetConfig->entityColumnLabel;
      //$dynamicValue = strtoupper($row[$node->widgetConfig->entityColumnName]);
      $dynamicValue = strtoupper($row[$node->widgetConfig->summaryView->entityColumnName ?? $node->widgetConfig->entityColumnName]);

      if($smnid == 720) {
        $noContr = WidgetUtil::getLabel("num_sub_contracts");
        $mwbe_category = '<strong>'.WidgetUtil::getLabel("mwbe_category").'</strong>: '.strtoupper(MappingUtil::getMinorityCategoryById($row['minority_type_minority_type']));
      }
      else if($smnid == 725) {
        $noContr = WidgetUtil::getLabel("num_sub_contracts");
        $mwbe_category = '<strong>'.WidgetUtil::getLabel("mwbe_category").'</strong>: '.strtoupper(MappingUtil::getMinorityCategoryById($row['prime_minority_type_prime_minority_type']));
      }
      else if($smnid == 726 || $smnid == 727 || $smnid == 728 || $smnid == 729) {
        $noContr = WidgetUtil::getLabel("num_sub_contracts");
      }
      else if($smnid == 783) {
        $mwbe_category = '<strong>'.WidgetUtil::getLabel("mwbe_category").'</strong>: '.strtoupper(MappingUtil::getMinorityCategoryById($row['current_prime_minority_type_id']));
      }
      else if($smnid == 784) {
        $mwbe_category = '<strong>'.WidgetUtil::getLabel("mwbe_category").'</strong>: '.strtoupper(MappingUtil::getMinorityCategoryById($row['minority_type_minority_type']));
      }
      else if($smnid == 791) {
        $noContr = WidgetUtil::getLabel("no_of_contracts");
        $mwbe_category = '<strong>'.WidgetUtil::getLabel("mwbe_category").'</strong>: '.strtoupper(MappingUtil::getMinorityCategoryById($row['minority_type_minority_type']));
      }

      if($smnid == 369 || $smnid == 785 || $smnid == 726 ) {
        $dynamicLabel= WidgetUtil::getLabel("award_method");
      }
      if($smnid == 370 || $smnid == 786 || $smnid == 727 ){
        $dynamicLabel= WidgetUtil::getLabel("contract_agency");
      }
      if($smnid == 454 || $smnid == 787 || $smnid == 728 ){
        $dynamicLabel= WidgetUtil::getLabel("industry_name");
      }
      if($smnid == 453 || $smnid == 788 || $smnid == 729 ){
        $dynamicLabel= WidgetUtil::getLabel("contract_size");
      }


      $originalAmount = FormattingUtilities::custom_number_formatter_format($row['original_amount_sum'],2,'$');
      $currentAmount = FormattingUtilities::custom_number_formatter_format($row['current_amount_sum'],2,'$');
      $spentToDateAmount = FormattingUtilities::custom_number_formatter_format($row['spending_amount_sum'],2,'$');
      $spnttodt = WidgetUtil::getLabel("spent_to_date");
      $oamnt = WidgetUtil::getLabel("original_amount");
      $camnt = WidgetUtil::getLabel("current_amount");
      $totalContracts = number_format($row['total_contracts']);
      $templateTitle = $node->widgetConfig->summaryView->templateTitle ?? $node->widgetConfig->templateTitle;

      $summaryContent =  <<<EOD
<div class="contract-details-heading">
	<div class="contract-id">
		<h2 class="contract-title">{$templateTitle}</h2>
	</div>
	<div class="dollar-amounts">
		<div class="spent-to-date">
			{$spentToDateAmount}
            <div class="amount-title">{$spnttodt}</div>
		</div>
		<div class="original-amount">
		    {$originalAmount}
            <div class="amount-title">{$oamnt}</div>
		</div>
		<div class="current-amount">
		    {$currentAmount}
        <div class="amount-title">{$camnt}</div>
	    </div>
	</div>
</div>
<div class="contract-information">
	<div class="no-of-contracts">
			{$totalContracts}
			<div class="amount-title">{$noContr}</div>
	</div>
	<ul>
	    <li class="contractid">
	        <span class="gi-list-item">{$dynamicLabel}:</span> {$dynamicValue}<br>{$mwbe_category}
		</li>
	</ul>
</div>
EOD;
     return $summaryContent;
    }
  }

  public function subContract($node) {
    $records = $node->data;
    if (is_array($records)) {
      $row = $records[0];
      $originalAmount = FormattingUtilities::custom_number_formatter_format($row['original_amount_sum'], 2, '$');
      $currentAmount = FormattingUtilities::custom_number_formatter_format($row['current_amount_sum'], 2, '$');
      $spentToDateAmount = FormattingUtilities::custom_number_formatter_format($row['spending_amount_sum'], 2, '$');
      $cont_id = WidgetUtil::getLabel("contract_id");
      $spnttodt = WidgetUtil::getLabel("spent_to_date");
      $oamnt = WidgetUtil::getLabel("original_amount");
      $camnt = WidgetUtil::getLabel("current_amount");
      $purpose = WidgetUtil::getLabel("contract_purpose");
      $agency = WidgetUtil::getLabel("contract_agency");
      $vendor = WidgetUtil::getLabel("sub_vendor_name");
      $smnid = RequestUtilities::get('smnid') ?? RequestUtilities::_getRequestParamValueBottomURL('smnid');
      if ($smnid == 721) {
        $purpose = WidgetUtil::getLabel("sub_contract_purpose");
        $associated_prime_vendor_value = strtoupper($row['vendor_vendor_legal_name']);
        $associated_prime_vendor = WidgetUtil::getLabel("associated_prime_vendor");
      }
      $agency_value = strtoupper($row['agency_agency_agency_name']);
      $purpose_value = strtoupper($row['contract_purpose_contract_purpose']);
      $vendor_value = strtoupper($row['subvendor_subvendor_legal_name']);

      $summaryContent = <<<EOD
      <div class="contract-details-heading">
        <div class="contract-id">
          <h2 class="contract-title">{$node->widgetConfig->templateTitle}</h2>
        </div>
        <div class="dollar-amounts">
          <div class="spent-to-date">
            {$spentToDateAmount}
                  <div class="amount-title">{$spnttodt}</div>
          </div>
          <div class="original-amount">
              {$originalAmount}
                  <div class="amount-title">{$oamnt}</div>
          </div>
          <div class="current-amount">
              {$currentAmount}
                  <div class="amount-title">{$camnt}</div>
          </div>
        </div>
      </div>
      <div class="contract-information">
        <ul>
            <li class="contractid">
                <span class="gi-list-item">{$cont_id}:</span> {$row['contract_number_contract_number']}
            </li>
          <li class="contract-purpose">
            <span class="gi-list-item">{$purpose}:</span> {$purpose_value}
              </li>
          <li class="agency">
            <span class="gi-list-item">{$agency}:</span> {$agency_value}
          </li>
          <li class="vendor">
            <span class="gi-list-item">{$vendor}:</span> {$vendor_value}
          </li>
          <li class="associated-prime-vendor">
            <span class="gi-list-item">{$associated_prime_vendor}:</span> {$associated_prime_vendor_value}
          </li>
        </ul>
      </div>
      EOD;
      $contract_num = RequestUtilities::getTransactionsParams('contnum');
      //Hide Contract summary on Contract Spending Transactions page (Total Spent to Date link under 'Sub Vendor Information' section)
      if ($smnid == 721 && preg_match("/^contract\/spending\/transactions/", \Drupal::request()->query->get('q')) && isset($contract_num)) {
        return "";
      }
      else {
        return $summaryContent;
      }
    }
  }

  public function pendingContractVendorInfo($node) {
    //TODO temp fix move bottom code to separate custom preprocess function
    //_getRequestParamValueBottomURL
    $contract_num = RequestUtilities::_getRequestParamValueBottomURL('contract');
    $contract_num = $contract_num ?? RequestUtilities::get('contract');

    $version_num = RequestUtilities::_getRequestParamValueBottomURL('version');
    $version_num = $version_num ?? RequestUtilities::get('version');

    $queryVendorDetails = "SELECT
       p.minority_type_id,
       vh.vendor_id,
       rb.business_type_code,
       p.vendor_id vendor_vendor,
       l444.document_code,
       va.address_id,
       p.vendor_legal_name AS vendor_name,
       a.address_line_1,
       a.address_line_2,
       a.city, a.state, a.zip, a.country,
      (CASE WHEN (rb.business_type_code = 'MNRT' OR rb.business_type_code = 'WMNO') THEN 'Yes' ELSE 'NO' END) AS mwbe_vendor,
      (CASE WHEN p.minority_type_id in (4,5) then 'Asian American' ELSE p.minority_type_name END)AS ethnicity
	                        FROM {pending_contracts} p
	                            LEFT JOIN {vendor} v ON p.vendor_id = v.vendor_id
	                            LEFT JOIN (SELECT vendor_id, MAX(vendor_history_id) AS vendor_history_id
	                                        FROM {vendor_history} WHERE miscellaneous_vendor_flag::BIT = 0 ::BIT  GROUP BY 1) vh ON v.vendor_id = vh.vendor_id
	                            LEFT JOIN {vendor_address} va ON vh.vendor_history_id = va.vendor_history_id
	                            LEFT JOIN {address} a ON va.address_id = a.address_id
	                            LEFT JOIN {ref_address_type} ra ON va.address_type_id = ra.address_type_id
	                            LEFT JOIN {vendor_business_type} vb ON vh.vendor_history_id = vb.vendor_history_id
	                            LEFT JOIN {ref_business_type} rb ON vb.business_type_id = rb.business_type_id
	                            LEFT JOIN {ref_minority_type} rm ON vb.minority_type_id = rm.minority_type_id
	                            LEFT JOIN {ref_document_code} AS l444 ON l444.document_code_id = p.document_code_id
	                        WHERE p.contract_number = '" . $contract_num . "'"
      ." AND p.document_version =" .$version_num;

    $results1 = _checkbook_project_execute_sql($queryVendorDetails);
    $node->data = $results1;
    foreach($node->data as $key => $value){
      if($value['business_type_code'] == "MNRT" || $value['business_type_code'] == "WMNO"){
        $node->data[0]["mwbe_vendor"] = "Yes";
      }
    }

    if($node->data[0]["vendor_id"]){
      $queryVendorCount = "SELECT COUNT(*) AS total_contracts_sum FROM {agreement_snapshot} WHERE latest_flag= 'Y' AND vendor_id =".$node->data[0]["vendor_id"];
      $results2 = _checkbook_project_execute_sql($queryVendorCount);

      foreach($results2 as $row){
        $total_cont +=$row['total_contracts_sum'];
      }

      if($node->data[0]["mwbe_vendor"] == "Yes"){
        $total_cont  = 0;
        $dashboard = RequestUtilities::_appendMWBESubVendorDatasourceUrlParams().'/dashboard/mp';
      }
      if($node->data[0]['document_code'] == 'RCT1')
        $vendor_link = '/contracts_pending_rev_landing/year/' . CheckbookDateUtil::getCurrentFiscalYearId() . '/yeartype/B'.$dashboard.'/vendor/'.$node->data[0]['vendor_vendor'] .'?expandBottomCont=true';
      else
        $vendor_link = '/contracts_pending_exp_landing/year/' . CheckbookDateUtil::getCurrentFiscalYearId() . '/yeartype/B'.$dashboard.'/vendor/'.$node->data[0]['vendor_vendor'] .'?expandBottomCont=true';
    }

    $return_value = "
    <ul class='left'>
    <li><span class=\"gi-list-item\">Prime Vendor:</span> <a href=\"{$vendor_link}\" >{$node->data[0]['vendor_name']}</a></li>
    ";

    $minority_type_id = $node->data[0]['minority_type_id'];
    $address = $node->data[0]['address_line_1'] ;
    $address .= " "  .  $node->data[0]['address_line_2'];
    $address .= " "  .  $node->data[0]['city'];
    $address .= " "  .  $node->data[0]['state'];
    $address .= " "  .  $node->data[0]['zip'];
    $address .= " "  .  $node->data[0]['country'];

    $ethnicities = array();
    foreach($node->data as $row){
      if($row['ethnicity'] != null and trim($row['ethnicity']) != '' ){
        $ethnicities[] =MappingUtil::getMinorityCategoryById($minority_type_id);
      }
    }
    $ethnicity = implode(',',array_unique($ethnicities));
    if($minority_type_id == "4" || $minority_type_id == "5"){
      $minority_type_id = "4~5";
    }

    $return_value .= "
    <li><span class=\"gi-list-item\">Address:</span> {$address}</li>
    <li><span class=\"gi-list-item\">Total Number of NYC Contracts:</span> {$total_cont}</li>
    <li><span class=\"gi-list-item\">M/WBE Vendor:</span> {$node->data[0]['mwbe_vendor']}</li>

    <li><span class=\"gi-list-item\">M/WBE Category:</span> {$ethnicity}</li>
</ul>
    ";

    print $return_value;
  }

  public function expenseContract($node)
  {
    $records = $node->data;
    if (is_array($records)) {
      $row = $records[0];

      $originalAmount = FormattingUtilities::custom_number_formatter_format($row['original_amount_sum'], 2, '$');
      $currentAmount = FormattingUtilities::custom_number_formatter_format($row['current_amount_sum'], 2, '$');
      $spentToDateAmount = FormattingUtilities::custom_number_formatter_format($row['spending_amount_sum'], 2, '$');
      $cont_id = WidgetUtil::getLabel("contract_id");
      $spnttodt = WidgetUtil::getLabel("spent_to_date");
      $oamnt = WidgetUtil::getLabel("original_amount");
      $camnt = WidgetUtil::getLabel("current_amount");
      $purpose = WidgetUtil::getLabel("contract_purpose");
      $agency = WidgetUtil::getLabel("contract_agency");
      $smnid = RequestUtilities::_getRequestParamValueBottomURL('smnid');

      $dynamicLabel = $node->widgetConfig->entityColumnLabel;
      $dynamicValue = strtoupper($row[$node->widgetConfig->entityColumnName]);

      if (!isset($dynamicLabel))
        $dynamicLabel = WidgetUtil::getLabel("vendor_name");
      $agency_value = strtoupper($row['agency_agency_agency_name']);
      $purpose_value = strtoupper($row['contract_purpose_contract_purpose']);
      $summaryContent = <<<EOD
<div class="contract-details-heading">
	<div class="contract-id">
		<h2 class="contract-title">{$node->widgetConfig->templateTitle}</h2>
	</div>
	<div class="dollar-amounts">
		<div class="spent-to-date">
			{$spentToDateAmount}
            <div class="amount-title">{$spnttodt}</div>
		</div>
		<div class="original-amount">
		    {$originalAmount}
            <div class="amount-title">{$oamnt}</div>
		</div>
		<div class="current-amount">
		    {$currentAmount}
            <div class="amount-title">{$camnt}</div>
		</div>
	</div>
</div>
<div class="contract-information">
	<ul>
	    <li class="contractid">
	        <span class="gi-list-item">{$cont_id}:</span> {$row['contract_number_contract_number']}
	    </li>
		<li class="contract-purpose">
			<span class="gi-list-item">{$purpose}:</span> {$purpose_value}
        </li>
		<li class="agency">
			<span class="gi-list-item">{$agency}:</span> {$agency_value}
		</li>
		<li class="vendor">
			<span class="gi-list-item">{$dynamicLabel}:</span> {$dynamicValue}
		</li>
	</ul>
</div>
EOD;

      //Hide Contract summary on Contract Spending Transactions page (Total Spent to Date link under 'Sub Vendor Information' section)
      $contract_num = RequestUtilities::getTransactionsParams('contnum');
      if ($smnid == 721 && preg_match("/^contract\/spending\/transactions/", \Drupal::request()->query->get('q')) && isset($contract_num)) {
        print "<div><div class='contract-details-heading'>
	<div class='contract-id'>
		<h2 class='contract-title'>{$node->widgetConfig->templateTitle}</h2>
	</div>
        </div></div>";
      } else {
        print $summaryContent;
      }
    }
  }

  /**
   * migrated from pending_transaction_title.tpl.php
   *
   * @return void
   */
  public function PendingTransactionTitle($contactCategoryLabel) {
    if (empty(RequestUtilities::getBottomContUrl())) {
      $current_url = RequestUtilities::getCurrentPageUrl();
    } else {
      $current_url = RequestUtilities::getBottomContUrl();
    }

    if (str_contains($current_url, '/contract/search/transactions') || str_contains($current_url, '/contract/all/transactions')) {
      $summaryTitle = "";
    }else if(_checkbook_check_is_mwbe_page()){
      $summaryTitle = MappingUtil::getCurrenEthnicityName()." ";
    }
    $summaryTitle .= NodeSummaryUtil::getInitNodeSummaryTitle();
    global $_checkbook_breadcrumb_title;
    $_checkbook_breadcrumb_title =  "$summaryTitle Pending $contactCategoryLabel Contracts Transactions";
    return $summaryTitle;
  }

  public function getTransactionYearVal($yearLabel) {
    $year = CheckbookDateUtil::_getYearValueFromID(RequestUtilities::getTransactionsParams('calyear') ?? RequestUtilities::getTransactionsParams('year'));
    $year = $yearLabel.$year;
    return $year;
  }

  public function isActiveExpenseContractsSubvendor() {
    $is_active_expense_contracts = str_starts_with(RequestUtilities::getCurrentPageUrl(), "contracts_landing") && RequestUtilities::get("status") == "A"
      && RequestUtilities::get("bottom_slider") != "sub_vendor";

    return $is_active_expense_contracts;
  }

  public static function contractTitle(){
    $refURL = \Drupal::request()->query->get('refURL');
    $title ='';
    if(RequestUtil::isExpenseContractPath($refURL) || RequestUtil::isRevenueContractPath($refURL)){
      $title = ContractsBreadcrumbs::getContractsPageTitle();
    }
    if(RequestUtil::isPendingExpenseContractPath($refURL) || RequestUtil::isPendingRevenueContractPath($refURL)){
      $title =  ContractsBreadcrumbs::getPendingContractsTitleDrilldown();
    }
    if(RequestUtil::isNYCHAContractPath($refURL) ){
      $title =  ContractsBreadcrumbs::getNychaContractsPageTitle();
    }
    return $title;
  }

  public static function contractTopamoutGrid($node) {
    if (isset($node->data) && is_array($node->data)) {
      $output = '';
      foreach ($node->data as $datarow) {
        $datarow['contract_number'] = _checkbook_check_isEDCPage() ? $datarow['contract_number_contract_number'] : $datarow['contract_number'];
        $datarow['maximum_contract_amount'] = _checkbook_check_isEDCPage() ? $datarow['current_amount_sum'] : $datarow['maximum_contract_amount'];
        $datarow['legal_name@checkbook:vendor'] = _checkbook_check_isEDCPage() ? $datarow['display_vendor_names'] : $datarow['legal_name@checkbook:vendor'];
        $datarow['agency_name@checkbook:agency'] = _checkbook_check_isEDCPage() ? $datarow['display_agency_display_agency_agency_name'] : $datarow['agency_name@checkbook:agency'];

        $output .= '<tr>
                <td><div>' . $datarow['contract_number'] . '</div></td>
                <td>' . $datarow['maximum_contract_amount'] . '</td>
                <td>&nbsp;&nbsp;&nbsp;</td>
                <td><div>' . $datarow['legal_name@checkbook:vendor'] . '</div></td>
                <td><div>' . $datarow['agency_name@checkbook:agency'] . '</div></td>
                <td>&nbsp;</td>
                </tr>';
      }
    }
    return $output;
  }

}
