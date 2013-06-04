<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
?>
<?php
$options = array('html'=>true);
$options_disabled = array('html'=>true,"attributes"=>array("class"=>"noclick"));

if(preg_match('/vendor/',$_GET['q'])){
  $budget_link = l('<span class="nav-title">Budget</span><br>&nbsp;'. custom_number_formatter_format(0 ,1,'$') ,'',$options_disabled);
  $revenue_link =  l('<span class="nav-title">Revenue</span><br>&nbsp;'. custom_number_formatter_format(0 ,1,'$'),'',$options_disabled);
  $payroll_link = l('<span class="nav-title">Payroll</span><br>'.custom_number_formatter_format(0 ,1,'$'),'',$options_disabled);  
}else{
  if($node->data[3]['budget_current'] == 0){
    $budget_link = l('<span class="nav-title">Budget</span><br>&nbsp;'. custom_number_formatter_format(0 ,1,'$') ,'',$options_disabled);
  }else{
    $budget_link = l('<span class="nav-title">Budget</span><br>&nbsp;'. custom_number_formatter_format($node->data[3]['budget_current'] ,1,'$'),RequestUtil::getTopNavURL("budget"),$options) ;
  }
  if($node->data[4]['revenue_amount_sum'] == 0 ){
    $revenue_link =  l('<span class="nav-title">Revenue</span><br>&nbsp;'. custom_number_formatter_format(0 ,1,'$'),'',$options_disabled);
  }else{
    $revenue_link =  l('<span class="nav-title">Revenue</span><br>&nbsp;'. custom_number_formatter_format($node->data[4]['revenue_amount_sum'] ,1,'$') ,RequestUtil::getTopNavURL("revenue"),$options);
  }
  if($node->data[1]['total_gross_pay'] == 0){
    $payroll_link = l('<span class="nav-title">Payroll</span><br>'.custom_number_formatter_format(0 ,1,'$'),'',$options_disabled);
  }else{
    $payroll_link = l('<span class="nav-title">Payroll</span><br>'.custom_number_formatter_format($node->data[1]['total_gross_pay'] ,1,'$'),RequestUtil::getTopNavURL("payroll"),$options);
  }  
}
if($node->data[2]['check_amount_sum']  == 0){
  $spending_link =  l('<span class="nav-title">Spending</span><br>'. custom_number_formatter_format(0 ,1,'$'),'',$options_disabled);
}else{
  $spending_link =  l('<span class="nav-title">Spending</span><br>'. custom_number_formatter_format($node->data[2]['check_amount_sum'] ,1,'$'),RequestUtil::getTopNavURL("spending"),$options);  
}
  
if($node->data[0]['current_amount_sum'] == 0){
  $contracts_link =  l('<span class="nav-title">Contracts</span><br>'. custom_number_formatter_format(0 ,1,'$'),'',$options_disabled);
}else{
  $contracts_link = l('<span class="nav-title">Contracts</span><br>'.custom_number_formatter_format($node->data[0]['current_amount_sum'],1,'$'),RequestUtil::getTopNavURL("contracts"),$options);  
}


if(preg_match('/yeartype\/C/',$_GET['q'])){
  $budget_link = l('<span class="nav-title">Budget</span><br>&nbsp;'. custom_number_formatter_format(0 ,1,'$') ,'',$options_disabled);
  $revenue_link =  l('<span class="nav-title">Revenue</span><br>&nbsp;'. custom_number_formatter_format(0 ,1,'$'),'',$options_disabled);
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
//TODO: remove placeholder &nbsp; when numbers under each domain are active
?>
<table class="expense">
  <tr>
    <td class="revenue<?php if($rclass){print $rclass;}?>"><div class="expense-container"><?php print $revenue_link ?></div><div class='indicator'></div></td>
    <td class="budget first<?php if($expclass){print $expclass;}?>"><div class="expense-container"><?php print $budget_link; ?></div><div class='indicator'></div></td>   
    <td class="spending<?php if($chclass){print $chclass;}?>"><div class="expense-container"><?php print $spending_link; ?></div><div class='indicator'></div></td>
    <td class="contracts<?php if($cclass){print $cclass;}?>"><div class="expense-container"><?php print $contracts_link ?></div><div class='indicator'></div></td>
    <td class="employees last<?php if($eclass){print $eclass;}?>"><div class="expense-container"><?php print $payroll_link ?></div><div class='indicator'></div></td>
  </tr>
</table>