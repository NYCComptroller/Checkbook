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
?>
<?php



$options = array('html'=>true);
$options_disabled = array('html'=>true,"attributes"=>array("class"=>"noclick"));

if(_checkbook_check_isEDCPage()){
    $contract_amount = $node->data[0]['current_amount_sum'];
    $spending_amount = $node->data[1]['check_amount_sum'];
}else{
    $contract_amount = $node->data[0]['current_amount_sum'];
    $spending_amount = $node->data[2]['check_amount_sum'];
}


  if($node->data[3]['budget_current'] == 0 ){
    $budget_link = l('<span class="nav-title">Budget</span><br>&nbsp;'. custom_number_formatter_format(0 ,1,'$') ,'',$options_disabled);
  }else{
    $budget_link = l('<span class="nav-title">Budget</span><br>&nbsp;'. custom_number_formatter_format($node->data[3]['budget_current'] ,1,'$'),RequestUtil::getTopNavURL("budget"),$options) ;
  }
  if($node->data[4]['revenue_amount_sum'] == 0 ){
    $revenue_link =  l('<span class="nav-title">Revenue</span><br>&nbsp;'. custom_number_formatter_format(0 ,1,'$'),'',$options_disabled);
  }else{
    $revenue_link =  l('<span class="nav-title">Revenue</span><br>&nbsp;'. custom_number_formatter_format($node->data[4]['revenue_amount_sum'] ,1,'$') ,RequestUtil::getTopNavURL("revenue"),$options);
  }
  if($node->data[1]['total_gross_pay'] == 0  ){
    $payroll_link = l('<span class="nav-title">Payroll</span><br>'.custom_number_formatter_format(0 ,1,'$'),'',$options_disabled);
  }else{
    $payroll_link = l('<span class="nav-title">Payroll</span><br>'.custom_number_formatter_format($node->data[1]['total_gross_pay'] ,1,'$'),RequestUtil::getTopNavURL("payroll"),$options);
  }  

if($spending_amount  == 0){
  $spending_link =  l('<span class="nav-title">Spending</span><br>'. custom_number_formatter_format(0 ,1,'$'),'',$options_disabled);
}else{
  $spending_link =  l('<span class="nav-title">Spending</span><br>'. custom_number_formatter_format($spending_amount ,1,'$'),RequestUtil::getTopNavURL("spending"),$options);
}
  
if($contract_amount == 0){
  $contracts_link =  l('<span class="nav-title">Contracts</span><br>'. custom_number_formatter_format(0 ,1,'$'),'',$options_disabled);
}else{
  $contracts_link = l('<span class="nav-title">Contracts</span><br>'.custom_number_formatter_format($contract_amount, 1,'$'),RequestUtil::getTopNavURL("contracts"),$options);
}

if(preg_match('/yeartype\/C/',$_GET['q'])){
  $budget_link = l('<span class="nav-title">Budget</span><br>&nbsp;'. custom_number_formatter_format(0 ,1,'$') ,'',$options_disabled);
  $revenue_link =  l('<span class="nav-title">Revenue</span><br>&nbsp;'. custom_number_formatter_format(0 ,1,'$'),'',$options_disabled);
}




// Disable featured dashboatrd for other government entities. 
if(preg_match('/datasource\/checkbook_oge/',$_GET['q'])){
	$mwbe_amount =  0;
	$svendor_amount = 0;
	$mwbe_filters =  "<div class='main-nav-drop-down' style='display:none'>
  		</div>
  		";
	$svendor_filters =  "<div class='main-nav-drop-down' style='display:none'>
  		</div>
  		";
}else{
	
	$is_mwbe_certified = MappingUtil::isMWBECertified(explode('~',_getRequestParamValue('mwbe')));
	$mwbe_prefix = ($is_mwbe_certified)? MappingUtil::$mwbe_prefix :'';
	
	if(preg_match('/contracts/',$_GET['q'])){
		$mwbe_amount = $node->data[6]['current_amount_sum'];
		$mwbe_active_domain_link = RequestUtil::getTopNavURL("contracts") ;
		$mwbe_active_domain_link = preg_replace('/\/mwbe\/[^\/]*/','',$mwbe_active_domain_link);
		$mwbe_active_domain_link = preg_replace('/\/subvendor\/[^\/]*/','',$mwbe_active_domain_link);
		$mwbe_filters = MappingUtil::getCurrentMWBETopNavFilters($mwbe_active_domain_link,"contracts");
		
		$svendor_amount = $node->data[8]['current_amount_sum'];
		$svendor_active_domain_link = RequestUtil::getTopNavURL("contracts") ;
		$svendor_active_domain_link = preg_replace('/\/subvendor\/[^\/]*/','',$svendor_active_domain_link);
		$total_sub_vendors_link = RequestUtil::getLandingPageUrl("contracts");
	}else{
		$mwbe_amount = $node->data[5]['check_amount_sum'];
		$mwbe_active_domain_link = RequestUtil::getTopNavURL("spending") ;
		$mwbe_active_domain_link = preg_replace('/\/mwbe\/[^\/]*/','',$mwbe_active_domain_link);
		$mwbe_active_domain_link = preg_replace('/\/subvendor\/[^\/]*/','',$mwbe_active_domain_link);
		$mwbe_filters = MappingUtil::getCurrentMWBETopNavFilters($mwbe_active_domain_link,"spending");
		
		$svendor_amount = $node->data[7]['check_amount_sum'];
		$svendor_active_domain_link = RequestUtil::getTopNavURL("spending") ;
		$svendor_active_domain_link = preg_replace('/\/subvendor\/[^\/]*/','',$svendor_active_domain_link);
		$total_sub_vendors_link = RequestUtil::getLandingPageUrl("spending");
	}
	
	
	$svendor_filters =  "<div class='main-nav-drop-down' style='display:none'>
			<ul>
  				<li class='no-click'><a href='/" . $total_sub_vendors_link . "/subvendors/all" . "'>Total SubVendors</a></li>
			</ul>
  		</div>
  		";
	
}

if($mwbe_amount  == 0){
	$mwbe_link = l('<div><span class="nav-title">M/WBE</span><br>&nbsp;'. custom_number_formatter_format(0 ,1,'$') . '</div>','',$options_disabled);
}else{	
	$mwbe_link = l('<div><span class="nav-title">M/WBE</span><br>&nbsp;'. custom_number_formatter_format($mwbe_amount ,1,'$') . '</div>',$mwbe_active_domain_link. "/mwbe/2~3~4~5~9",$options);
}


if($svendor_amount  == 0){
	$subvendors_link = l('<div><span class="nav-title">' .$mwbe_prefix  .' Sub Vendors</span><br>&nbsp;'. custom_number_formatter_format(0 ,1,'$') . '</div>','',$options_disabled);	
}else{
	$subvendors_link = l('<div><span class="nav-title">' .$mwbe_prefix  .' Sub Vendors</span><br>&nbsp;'. custom_number_formatter_format($svendor_amount ,1,'$') . '</div>',$svendor_active_domain_link. "/subvendor/all",$options);
}


 

$expclass = '';
$rclass = '';
$cclass = '';
$chclass = '';
$eclass = '';
$vclass = '';
$arg = arg(0);
switch ($arg){
  case 'budget':
    $expclass = ' active';
    break;
  case 'revenue':
    $rclass = ' active';
    break;
  case 'contract':
  case 'contracts_landing':
  case 'contracts_revenue_landing':
  case 'contracts_pending_rev_landing':
  case 'contracts_pending_exp_landing':
    $cclass = ' active';
    break;
  case 'spending_landing':
  case 'spending':
    $chclass = ' active';
    break;
  case 'payroll':
    $eclass = ' active';
    break;
}

if(preg_match('/mwbe/',$_GET['q']) && preg_match('/subvendor/',$_GET['q']) ){
	$mwbeclass = ' active';
	$svclass = ' active';
	$featured_dashboard = 'subvendor';
}elseif(preg_match('/mwbe/',$_GET['q']) ){
	$mwbeclass = ' active';
	$svclass = ' active';
	$featured_dashboard = 'mwbe';	
}elseif(preg_match('/subvendor/',$_GET['q']) ){
	$svclass = ' active';
	$featured_dashboard = 'subvendor';	
}


if(!preg_match('/smnid/',$_GET['q']) && (
		preg_match('/spending\/transactions/',$_GET['q'])
		|| preg_match('/contract\/all\/transactions/',$_GET['q'])
		|| preg_match('/contract\/search\/transactions/',$_GET['q'])
	)
	
){
	$mwbeclass = ' ';
}

//TODO: remove placeholder &nbsp; when numbers under each domain are active


?>
<div class="top-navigation-left">
<table class="expense">
  <tr>
    <td class="budget first<?php if($expclass){print $expclass;}?>"><div class="expense-container"><?php print $budget_link; ?></div><div class='indicator'></div></td>   
    <td class="revenue<?php if($rclass){print $rclass;}?>"><div class="expense-container"><?php print $revenue_link ?></div><div class='indicator'></div></td>   
    <td class="spending<?php if($chclass){print $chclass;}?>"><div class="expense-container"><?php print $spending_link; ?></div>
    				<?php if(!$mwbeclass){?><div class='indicator'></div><?php }?></td>
    <td class="contracts<?php if($cclass){print $cclass;}?>"><div class="expense-container"><?php print $contracts_link ?></div>
    				<?php if(!$mwbeclass){?><div class='indicator'></div><?php }?></td>
    <td class="employees<?php if($eclass){print $eclass;}?>"><div class="expense-container"><?php print $payroll_link ?></div><div class='indicator'></div></td>
  </tr>
</table>

</div>


<div class="top-navigation-right">
<div class="featured-dashboard-title"><span>Featured Dashboard</span></div>
<div class="featured-dashboard-table">
<table class="expense">
  <tr>
    <td class="mwbe<?php if($mwbeclass){print $mwbeclass;}?>"><div class="expense-container"><?php print $mwbe_link ?><div><?php print $mwbe_filters; ?></div></div>
    			<?php if($featured_dashboard == "mwbe"){?><div class='indicator'></div><?php }?></td>
    <td class="mwbe subvendors<?php if($svclass){print $svclass;}?>"><div class="expense-container"><?php print $subvendors_link ?><div><?php print $svendor_filters; ?></div></div>
    			<?php if($featured_dashboard == "subvendor"){?><div class='indicator'></div><?php }?></td>
  </tr>
</table>
</div>
</div>