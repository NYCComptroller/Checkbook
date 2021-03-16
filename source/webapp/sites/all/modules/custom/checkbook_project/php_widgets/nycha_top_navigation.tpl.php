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


/**
 *  Set Spending, Budget & Revenue domains to "0" for NYCHA
 *  This logic should only apply to the landing & transaction pages from the details links, NOT the advanced search pages.
 */
$options = array('html'=>true);
$options_disabled = array('html'=>true,"attributes"=>array("class"=>"noclick"));

$budget_link = l('<span class="nav-title">Budget</span><br>&nbsp;'. custom_number_formatter_format(0 ,1,'$') ,'',$options_disabled);
$revenue_link = l('<span class="nav-title">Revenue</span><br>&nbsp;'. custom_number_formatter_format(0 ,1,'$'),'',$options_disabled);
$spending_link =  l('<span class="nav-title">Spending</span><br>'. custom_number_formatter_format(0 ,1,'$'),'',$options_disabled);
$contracts_link = l('<span class="nav-title">Contracts</span><br>'.custom_number_formatter_format(0 ,1,'$'),'',$options_disabled);
$payroll_link = l('<span class="nav-title">Payroll</span><br>'.custom_number_formatter_format(0 ,1,'$'),'',$options_disabled);

//Payroll Link
if($node->data[0]['total_gross_pay'] > 0  ){
    $payroll_link = l('<span class="nav-title">Payroll</span><br>'.custom_number_formatter_format($node->data[0]['total_gross_pay'] ,1,'$'),RequestUtil::getTopNavURL("payroll"),$options);
}

//Contracts Link
if($node->data[1]['total_maximum_contract_amount'] > 0) {
    $contracts_link = l('<span class="nav-title">Contracts</span><br>' . custom_number_formatter_format($node->data[1]['total_maximum_contract_amount'], 1, '$'), RequestUtil::getTopNavURL("nycha_contracts"), $options);
}

//Budget Link
$node->data[2]['budget_adopted_amount'] = 0;
if($node->data[2]['budget_adopted_amount'] > 0) {
  $budget_link = l('<span class="nav-title">Budget</span><br>' . custom_number_formatter_format($node->data[2]['budget_adopted_amount'], 1, '$'), RequestUtil::getTopNavURL("nycha_budget"), $options);
}

//Revenue Link
$node->data[3]['revenue_recognized_amount'] = 0;
if($node->data[3]['revenue_recognized_amount'] > 0) {
  $revenue_link = l('<span class="nav-title">Revenue</span><br>' . custom_number_formatter_format($node->data[3]['revenue_recognized_amount'], 1, '$'), RequestUtil::getTopNavURL("nycha_revenue"), $options);
}

//Spending Link
$total_spending = 0;
foreach($node->data as $key=>$row){
  $row['invoice_amount_sum'] = ($row['category_name_category_name'] == 'Payroll') ? $row['check_amount_sum'] : $row['invoice_amount_sum'];
  $total_spending +=  $row['invoice_amount_sum'];
}
if($total_spending > 0  ) {
  $spending_link = l('<span class="nav-title">Spending</span><br>' . custom_number_formatter_format($total_spending, 1, '$'), RequestUtil::getTopNavURL("nycha_spending"), $options);
}

$arg = arg(0);
$budget_active = '';
$revenue_active = '';
$contracts_active = '';
$spending_active = '';
$payroll_active = '';
switch ($arg){
    case 'nycha_budget':
        $budget_active = ' active';
        break;
    case 'nycha_revenue':
        $revenue_active = ' active';
        break;
    case 'nycha_contracts':
        $contracts_active = ' active';
        break;
    case 'nycha_spending':
        $spending_active = ' active';
        break;
    case 'payroll':
        $payroll_active = ' active';
        break;
}

//css to indicate no child menus for featured dashboards
$feature_db_css = "expense-container";
?>
<div class="top-navigation-left">
    <table class="expense">
        <tr>
            <td class="budget first<?php if($budget_active){print $budget_active;}?>">
                <div class="expense-container"><?php print $budget_link; ?></div>
                <div class='indicator'></div>
            </td>
            <td class="revenue<?php if($revenue_active){print $revenue_active;}?>">
                <div class="expense-container"><?php print $revenue_link ?></div>
                <div class='indicator'></div>
            </td>
            <td class="spending<?php if($spending_active){print $spending_active;}?>">
                <div class="expense-container"><?php print $spending_link; ?></div>
                <div class='indicator'></div>
            </td>
            <td class="contracts<?php if($contracts_active){print $contracts_active;}?>">
                <div class="expense-container"><?php print $contracts_link ?></div>
               <div class='indicator'></div>
            </td>
            <td class="employees<?php if($payroll_active){print $payroll_active;}?>">
                <div class="expense-container"><?php print $payroll_link ?></div>
                <div class='indicator'></div>
            </td>
        </tr>
    </table>
</div>
<?php
    //M/WBE and Section links of NYCHA Contracts
    // Disabling link for nycha feature board
    //$mwbe_link = l('<span class="nav-title">M/WBE</span><br>'.custom_number_formatter_format(0 ,1,'$'),RequestUtil::getTopNavURL("nycha_contracts"),$options);
    //$section_link = l('<span class="nav-title">Section 3</span><br>'.custom_number_formatter_format(0 ,1,'$'),RequestUtil::getTopNavURL("nycha_contracts"),$options);
    $mwbe_link = l('<span class="nav-title">M/WBE</span><br>'.custom_number_formatter_format(0 ,1,'$'),'',$options_disabled);
    $section_link = l('<span class="nav-title">Section 3</span><br>'.custom_number_formatter_format(0 ,1,'$'),'',$options_disabled);
    //css to indicate no child menus for featured dashboards
    //Remove no-menu when M/WBE and Subvendors dashboards are implemented
    $fdexpclass = "expense-container no-menu";
?>

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
                        <?php print $section_link; print '<div class="drop-down-menu-triangle">'  . $svendor_filters .'</div>' ?>
                    </div>
                    <?php if($featured_dashboard == "sp" ||$featured_dashboard == "ss"){?>
                        <div class='indicator'></div>
                    <?php }?>
                </td>
            </tr>
        </table>
    </div>
</div>
