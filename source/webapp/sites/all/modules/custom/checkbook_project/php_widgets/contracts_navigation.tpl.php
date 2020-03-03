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
$options = array('html'=>true);
$options_disabled = array('html'=>true,"attributes"=>array("class"=>"noclick"));

if(_checkbook_check_isEDCPage()){
    $contract_amount = $node->data[0]['current_amount_sum'];
    $spending_amount = $node->data[1]['check_amount_sum'];
}else if(_checkbook_check_isNYCHAPage()){
    $node->data[1]['total_gross_pay'] = $node->data[0]['total_gross_pay'];
}else{
    $contract_amount = $node->data[0]['current_amount_sum'];
    $spending_amount = $node->data[2]['check_amount_sum'];
}

/**
 *  Set Budget, Payroll & Revenue domains to "0" and disable them if a vendor was present in the URL.
 *  This logic should only apply to the landing & transaction pages from the details links, NOT the advanced search pages.
 */
$urlPath = $_GET['q'];
$ajaxPath = $_SERVER['HTTP_REFERER'];
$contracts_advanced_search = $spending_advanced_search = false;
if(preg_match('/spending\/search\/transactions/',$urlPath) || preg_match('/spending\/search\/transactions/',$ajaxPath)) {
    $spending_advanced_search = true;
}
if(preg_match('/contract\/all\/transactions/',$urlPath) || preg_match('/contract\/all\/transactions/',$ajaxPath) ||
    preg_match('/contract\/search\/transactions/',$urlPath) || preg_match('/contract\/search\/transactions/',$ajaxPath)) {
    $contracts_advanced_search = true;
}
$has_vendor_parameter = preg_match('/\/vendor/',$_GET['q']);

if($has_vendor_parameter && (!$contracts_advanced_search && !$spending_advanced_search)){
    $budget_link = l('<span class="nav-title">Budget</span><br>&nbsp;'. custom_number_formatter_format(0 ,1,'$') ,'',$options_disabled);
    $revenue_link = l('<span class="nav-title">Revenue</span><br>&nbsp;'. custom_number_formatter_format(0 ,1,'$'),'',$options_disabled);
    $payroll_link = l('<span class="nav-title">Payroll</span><br>'.custom_number_formatter_format(0 ,1,'$'),'',$options_disabled);
}else{
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
}

if($spending_amount  == 0){
    $spending_link =  l('<span class="nav-title">Spending</span><br>'. custom_number_formatter_format(0 ,1,'$'),'',$options_disabled);
}else{
    $spending_link =  l('<span class="nav-title">Spending</span><br>'. custom_number_formatter_format($spending_amount ,1,'$'),RequestUtil::getTopNavURL("spending"),$options);
}

$current_dashboard = RequestUtilities::get("dashboard");

if(!_checkbook_check_isNYCHAPage()) {
    if ($contract_amount == 0) {
        //Check if there are any Active contracts when the registered amount is zero to enable 'Contracts' domain
        if ($node->data[14]['total_contracts'] > 0) {
            $contracts_url = RequestUtil::getTopNavURL("contracts");
            $contracts_link = l('<span class="nav-title">Contracts</span><br>' . custom_number_formatter_format(0, 1, '$'), $contracts_url, $options);
        } else {
            $contracts_url = RequestUtil::getTopNavURL("contracts");
            $contracts_link = ($contracts_url) ? l('<span class="nav-title">Contracts</span><br>' . custom_number_formatter_format(0, 1, '$'), $contracts_url, $options) : l('<span class="nav-title">Contracts</span><br>' . custom_number_formatter_format(0, 1, '$'), '', $options_disabled);
        }
    } else {
        $contracts_link = l('<span class="nav-title">Contracts</span><br>' . custom_number_formatter_format($contract_amount, 1, '$'), RequestUtil::getTopNavURL("contracts"), $options);
    }
}else{
    $contracts_link = l('<span class="nav-title">Contracts</span><br>' . custom_number_formatter_format(0, 1, '$'), '', $options_disabled);
}

// Disable featured dashboatrd for other government entities.
if(preg_match('/datasource\/checkbook_oge/',$_GET['q']) || preg_match('/datasource\/checkbook_nycha/',$_GET['q'])){
    $mwbe_amount =  0;
    $svendor_amount = 0;
    $mwbe_filters =  "<div class='main-nav-drop-down' style='display:none'></div>";
    $svendor_filters =  "<div class='main-nav-drop-down' style='display:none'></div>";
}else{
    // Get mwbe and subvendor links.
    $mwbe_active_domain_link = RequestUtil::getDashboardTopNavURL("mwbe") ;
    $svendor_active_domain_link = RequestUtil::getDashboardTopNavURL("subvendor") ;
    $svendor_active_domain_link = preg_replace('/\/industry\/[^\/]*/','',$svendor_active_domain_link);

    // calcluate amount for mwbe and subvendors top nav.
    if(preg_match('/contract/',$_GET['q'])){

        /*For M/WBE and Sub Vendors dashboard, need to consider both active & registered expense contracts for highlighting.
        This will resolve the case where there is active contracts, so user should be able to click on the dashboards. */

        /* Active Contracts */
        $active_mwbe_amount = $node->data[11]['current_amount_sum'];
        $active_mwbe_subven_amount = $node->data[13]['current_amount_sum'];
        $active_subven_amount = $node->data[12]['current_amount_sum'];

        /* Registered Contracts */
        $registered_mwbe_amount = $node->data[6]['current_amount_sum'];
        $registered_mwbe_subven_amount = $node->data[10]['current_amount_sum'];
        $registered_subven_amount = $node->data[8]['current_amount_sum'];

        /* Active & Registered Contracts */
        $active_registered_mwbe_amount = $active_mwbe_amount + $registered_mwbe_amount;
        $active_registered_mwbe_subven_amount = $active_mwbe_subven_amount + $registered_mwbe_subven_amount;
        $active_registered_subven_amount = $active_subven_amount + $registered_subven_amount;

        // for prime flow include prime + sub; for sub vendor flow include sub.
        if($current_dashboard == "mp" || $current_dashboard == "sp" || $current_dashboard == null){
            $mwbe_amount = $registered_mwbe_amount;
            $mwbe_subven_amount = $registered_mwbe_subven_amount;

            $mwbe_amount_active_inc = $active_registered_mwbe_amount;
            $mwbe_subven_amount_active_inc = $active_registered_mwbe_subven_amount;
        }else{
            $mwbe_amount = $registered_mwbe_subven_amount;
            $mwbe_subven_amount = 0;

            $mwbe_amount_active_inc = $active_registered_mwbe_subven_amount;
            $mwbe_subven_amount_active_inc = 0;
        }

        $mwbe_prime_amount = $registered_mwbe_amount;
        $svendor_amount = $registered_subven_amount;

        $mwbe_prime_amount_active_inc = $active_registered_mwbe_amount;
        $svendor_amount_active_inc = $active_registered_subven_amount;

        // if prime is zero and sub amount is not zero. change dashboard to ms
        if( $mwbe_prime_amount_active_inc ==  0  && $mwbe_subven_amount_active_inc > 0){
            $mwbe_amount += $mwbe_subven_amount;
            $mwbe_amount_active_inc += $mwbe_subven_amount_active_inc;
            RequestUtil::$is_prime_mwbe_amount_zero_sub_mwbe_not_zero = true;
            $mwbe_active_domain_link = preg_replace('/\/dashboard\/../','/dashboard/ms',$mwbe_active_domain_link);
        }

        // call function to get mwbe drop down filters.
        $mwbe_filters = MappingUtil::getCurrentMWBETopNavFilters($mwbe_active_domain_link,"contracts");

        // call function to get sub vendors drop down filters.
        $svendor_filters = MappingUtil::getCurrentSubVendorsTopNavFilters($svendor_active_domain_link,"contracts");
    }else{
        //for prime flow include prime + sub; for sub vendor flow include sub.
        if($current_dashboard == "mp" || $current_dashboard == "sp" || $current_dashboard == null){
            $mwbe_amount = $node->data[5]['check_amount_sum'];
            $mwbe_subven_amount = $node->data[9]['check_amount_sum'];
        }else{
            $mwbe_amount =  $node->data[9]['check_amount_sum'];
            $mwbe_subven_amount = 0;
        }

        $mwbe_prime_amount = $node->data[5]['check_amount_sum'];
        // if prime is zero and sub amount is not zero. change dashboard to ms
        if( $mwbe_prime_amount == null && $mwbe_subven_amount > 0){
            $mwbe_amount += $mwbe_subven_amount;
            RequestUtil::$is_prime_mwbe_amount_zero_sub_mwbe_not_zero = true;
            $mwbe_active_domain_link = preg_replace('/\/dashboard\/../','/dashboard/ms',$mwbe_active_domain_link);
        }

        // call function to get mwbe drop down filters.
        $mwbe_filters = MappingUtil::getCurrentMWBETopNavFilters($mwbe_active_domain_link,"spending");

        // call function to get sub vendors drop down filters.
        $svendor_filters = MappingUtil::getCurrentSubVendorsTopNavFilters($svendor_active_domain_link,"spending");
        $svendor_amount = $node->data[7]['check_amount_sum'];
    }
}
// tm_wbe is an exception case for total MWBE link. When prime data is not present but sub data is present for the agency vendor combination.
if(RequestUtilities::get("tm_wbe") == 'Y'){
    $svendor_amount = $mwbe_amount;
}

// make amounts zero for non mwbe and indviduals and others mwbe categories.
if(preg_match('/mwbe\/7/',$_GET['q']) || preg_match('/mwbe\/11/',$_GET['q'])){
    $mwbe_amount = 0;
    $svendor_amount  == 0;
}

// dont hightlight mwbe for advanced search pages.
if(!preg_match('/smnid/',$_GET['q']) && (preg_match('/spending\/transactions/',$_GET['q'])|| preg_match('/contract\/all\/transactions/',$_GET['q'])
        || preg_match('/contract\/search\/transactions/',$_GET['q']))){
	$mwbeclass = ' ';
}

$featured_dashboard = RequestUtilities::get("dashboard");

if($mwbe_amount  == 0 && $mwbe_amount_active_inc == 0){
    $mwbe_link = l('<div><div class="top-navigation-amount"><span class="nav-title">' . RequestUtil::getDashboardTopNavTitle("mwbe") . '</span><br>&nbsp;'. custom_number_formatter_format(0 ,1,'$') . '</div></div>','',$options_disabled);
}else{
    //Contracts-M/WBE(Subvendors) should be navigated to third bottom slider only when active contracts amount is zero
    if((!isset($mwbe_amount) || $mwbe_amount == 0) && preg_match('/contract/',$_GET['q']) && !_checkbook_check_isEDCPage() && RequestUtil::getDashboardTopNavTitle("mwbe") == 'M/WBE (Sub Vendors)'){

        $mwbe_active_domain_link = ContractURLHelper::prepareSubvendorContractsSliderFilter($mwbe_active_domain_link, 'ms', ContractURLHelper::thirdBottomSliderValue());
    }
    $mwbe_link = l('<div><div class="top-navigation-amount"><span class="nav-title">' . RequestUtil::getDashboardTopNavTitle("mwbe") . '</span><br>&nbsp;'. custom_number_formatter_format($mwbe_amount ,1,'$') . '</div></div>',$mwbe_active_domain_link,$options);
}

$indicator_left = true;
if($featured_dashboard != null){
    $indicator_left = false;
}else{
    $indicator_left = true;
}

if($svendor_amount  == 0 && $svendor_amount_active_inc == 0){
    if($svendor_amount_active_inc == 0 && preg_match('/contract/',$_GET['q']) && !_checkbook_check_isEDCPage() && ContractUtil::checkStatusOfSubVendorByPrimeCounts()){
        $dashboard = (isset($featured_dashboard) && $featured_dashboard == 'mp')? 'sp': 'ss';
//        $svendor_active_domain_link = ContractURLHelper::prepareSubvendorContractsSliderFilter('contracts_landing', $dashboard, TRUE);
        $subvendors_link = l('<div><div class="top-navigation-amount"><span class="nav-title">' .RequestUtil::getDashboardTopNavTitle("subvendor")  .'</span><br>&nbsp;'. custom_number_formatter_format($svendor_amount ,1,'$') . '</div></div>',$svendor_active_domain_link ,$options);
    }else{
        $subvendors_link = l('<div><div class="top-navigation-amount"><span class="nav-title">' .RequestUtil::getDashboardTopNavTitle("subvendor")  .'</span><br>&nbsp;'. custom_number_formatter_format(0 ,1,'$') . '</div></div>','',$options_disabled);
    }
}else{
    $subvendors_link = l('<div><div class="top-navigation-amount"><span class="nav-title">' .RequestUtil::getDashboardTopNavTitle("subvendor")  .'</span><br>&nbsp;'. custom_number_formatter_format($svendor_amount ,1,'$') . '</div></div>',$svendor_active_domain_link ,$options);
}

// conditions for making mwbe active.
if($featured_dashboard == "mp" ||$featured_dashboard == "ms" || ($featured_dashboard != null && ($mwbe_amount > 0 || $mwbe_amount_active_inc > 0) ) || RequestUtil::$is_prime_mwbe_amount_zero_sub_mwbe_not_zero ){
    $mwbeclass = ' active';
}
if( $featured_dashboard == "sp" || $featured_dashboard == "ss" || ($featured_dashboard != null && ($svendor_amount > 0 || $svendor_amount_active_inc > 0) ) || RequestUtil::$is_prime_mwbe_amount_zero_sub_mwbe_not_zero ){
    $svclass = ' active';
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

//css to indicate no child menus for featured dashboards
$fdexpclass = "expense-container";
if(_checkbook_check_isEDCPage()) {
    $fdexpclass .= " no-menu";
}


//TODO: remove placeholder &nbsp; when numbers under each domain are active
?>
<div class="top-navigation-left">
    <table class="expense">
        <tr>
            <td class="budget first<?php if($expclass){print $expclass;}?>">
                <div class="expense-container"><?php print $budget_link; ?></div>
                <div class='indicator'></div>
            </td>
            <td class="revenue<?php if($rclass){print $rclass;}?>">
                <div class="expense-container"><?php print $revenue_link ?></div>
                <div class='indicator'></div>
            </td>
            <td class="spending<?php if($chclass){print $chclass;}?>">
                <div class="expense-container"><?php print $spending_link; ?></div>
                <?php if($indicator_left){?><div class='indicator'></div><?php }?>
            </td>
            <td class="contracts<?php if($cclass){print $cclass;}?>">
                <div class="expense-container"><?php print $contracts_link ?></div>
                <?php if($indicator_left){?><div class='indicator'></div><?php }?>
            </td>
            <td class="employees<?php if($eclass){print $eclass;}?>">
                <div class="expense-container"><?php print $payroll_link ?></div>
                <div class='indicator'></div>
            </td>
        </tr>
    </table>
</div>

<div class="top-navigation-right">
    <div class="featured-dashboard-title"><a  alt="The amounts represented in the featured dashboards are subset amounts of either the Spending or Contract Domains">
        <?php echo (preg_match('/contract/',$_GET['q']))?"Contracts ":"Spending " ;?>Featured Dashboard</a>
    </div>
    <div class="featured-dashboard-table">
        <table class="expense">
            <tr>
                <td class="mwbe<?php if($mwbeclass){print $mwbeclass;}?>">
                    <div class="<?php print $fdexpclass;?>"><?php print $mwbe_link ?>
                        <?php print '<div class="drop-down-menu-triangle">'  . $mwbe_filters .'</div>' ?>
                    </div>
                    <?php if($featured_dashboard == "mp" ||$featured_dashboard == "ms"){?>
                        <div class='indicator'></div>
                    <?php }?>
                </td>
                <td class="mwbe subvendors<?php if($svclass){print $svclass;}?>">
                    <div class="<?php print $fdexpclass;?>">
                        <?php print $subvendors_link; print '<div class="drop-down-menu-triangle">'  . $svendor_filters .'</div>' ?>
                    </div>
                    <?php if($featured_dashboard == "sp" ||$featured_dashboard == "ss"){?>
                        <div class='indicator'></div>
                    <?php }?>
                </td>
            </tr>
        </table>
    </div>
</div>
