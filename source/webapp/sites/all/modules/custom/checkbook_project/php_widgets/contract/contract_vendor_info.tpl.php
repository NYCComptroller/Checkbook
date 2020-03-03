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
<h4>
Prime Vendor Information
</h4>
<?php


//TODO temp fix move bottom code to separate custom preprocess function
if(RequestUtilities::get("magid") != ""){
  $ag_id = RequestUtilities::get("magid");
}else{
  $ag_id = RequestUtilities::get("agid");
}

if(_get_current_datasource() != "checkbook_oge"){
  $queryVendorDetails = "SELECT cvlmc.minority_type_id, fa.contract_number, rb.business_type_code, fa.agreement_id,fa.original_agreement_id, 
                                fa.vendor_id, va.address_id, ve.legal_name AS vendor_name, a.address_line_1, a.address_line_2, a.city, a.state, a.zip, a.country,
                                (CASE WHEN cvlmc.minority_type_id IN (2,3,4,5,9) THEN 'Yes' ELSE 'NO' END) AS mwbe_vendor, 
                                (CASE WHEN cvlmc.minority_type_id IN (4,5) then 'Asian American' ELSE rm.minority_type_name END) AS ethnicity 
	                        FROM agreement_snapshot fa
	                            LEFT JOIN vendor_history vh ON fa.vendor_history_id = vh.vendor_history_id
	                            LEFT JOIN vendor as ve ON ve.vendor_id = vh.vendor_id
	                            LEFT JOIN vendor_address va ON vh.vendor_history_id = va.vendor_history_id
	                            LEFT JOIN address a ON va.address_id = a.address_id
	                            LEFT JOIN ref_address_type ra ON va.address_type_id = ra.address_type_id
	                            LEFT JOIN vendor_business_type vb ON vh.vendor_history_id = vb.vendor_history_id
	                            LEFT JOIN ref_business_type rb ON vb.business_type_id = rb.business_type_id
	                            LEFT JOIN contract_vendor_latest_mwbe_category cvlmc ON cvlmc.vendor_id = fa.vendor_id
                                LEFT JOIN ref_minority_type rm ON cvlmc.minority_type_id = rm.minority_type_id
	                        WHERE ra.address_type_code = 'PR' AND fa.latest_flag = 'Y' AND cvlmc.latest_minority_flag ='Y' AND fa.original_agreement_id = " . $ag_id. " 
	                        ORDER BY cvlmc.year_id DESC LIMIT 1";
}else{
  $queryVendorDetails = "SELECT  fa.contract_number, rb.business_type_code, fa.agreement_id,fa.original_agreement_id,
                                  fa.vendor_id, va.address_id, ve.legal_name AS vendor_name, a.address_line_1, a.address_line_2, a.city, a.state, a.zip, a.country
	                       FROM agreement_snapshot fa
	                            LEFT JOIN vendor_history vh ON fa.vendor_history_id = vh.vendor_history_id
	                            LEFT JOIN vendor as ve ON ve.vendor_id = vh.vendor_id
	                            LEFT JOIN vendor_address va ON vh.vendor_history_id = va.vendor_history_id
	                            LEFT JOIN address a ON va.address_id = a.address_id
	                            LEFT JOIN ref_address_type ra ON va.address_type_id = ra.address_type_id
	                            LEFT JOIN vendor_business_type vb ON vh.vendor_history_id = vb.vendor_history_id
	                            LEFT JOIN ref_business_type rb ON vb.business_type_id = rb.business_type_id
	                            LEFT JOIN ref_minority_type rm ON vb.minority_type_id = rm.minority_type_id
	                       WHERE ra.address_type_code = 'PR' AND fa.latest_flag = 'Y' AND fa.original_agreement_id = " . $ag_id. "LIMIT 1";
}


$queryVendorCount = " select count(*) total_contracts_sum from {agreement_snapshot} where vendor_id =
(select vendor_id from {agreement_snapshot} where original_agreement_id =". $ag_id . "limit 1)
   and latest_flag = 'Y'";



$results1 = _checkbook_project_execute_sql_by_data_source($queryVendorDetails,_get_current_datasource());
$node->data = $results1;

$total_cont  = 0;
$results2 = _checkbook_project_execute_sql_by_data_source($queryVendorCount,_get_current_datasource());
if(RequestUtilities::get("status")){
    $status = '/status/'.RequestUtilities::get("status");
}else{
    $status = '/status/A';
}

foreach($results2 as $row){
    $total_cont +=$row['total_contracts_sum'];
}

$vendor_link = _checkbook_vendor_link($node->data[0]['vendor_id'], TRUE);

$contract_number = $node->data[0]['contract_number'];

?>
  <ul class="left">
  <?php if( _get_current_datasource() == "checkbook" && !preg_match('/newwindow/',$_GET['q'])){?>
    <li><span class="gi-list-item">Prime Vendor:</span> <a href="<?php echo $vendor_link;?> " ><?php echo $node->data[0]['vendor_name'] ;?></a></li>
  <?php }else{ ?>
  	<li><span class="gi-list-item">Prime Vendor:</span> <?php echo $node->data[0]['vendor_name'] ;?></li>
  <?php } ?>
  <?php
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
          $ethnicities[] = MappingUtil::getMinorityCategoryById($minority_type_id);
        }
      }
      $ethnicity = implode(',',$ethnicities);
      if($minority_type_id == "4" || $minority_type_id == "5"){
        $minority_type_id = "4~5";
      }

  ?>
    <li><span class="gi-list-item">Address:</span> <?php echo $address;?></li>
    <li><span class="gi-list-item">Total Number of NYC Contracts:</span> <?php echo $total_cont;?></li>

<?php if( _get_current_datasource() == "checkbook" ){?>
    <!-- Total Number of Sub Vendors -->
    <li><span class="gi-list-item">M/WBE Vendor:</span> <?php echo $node->data[0]['mwbe_vendor'] ;?></li>


<?php if(!preg_match('/newwindow/',$_GET['q']) && $node->data[0]["mwbe_vendor"] == 'Yes' && RequestUtilities::get("doctype") == "RCT1") { ?>
        <li><span class="gi-list-item">M/WBE Category:</span> <a href="/contracts_revenue_landing<?php echo $status;?>/yeartype/B/year/<?php echo _getFiscalYearID();?>/mwbe/<?php echo $minority_type_id; ?>/dashboard/mp"><?php echo $ethnicity ;?></a></li>
<?php } elseif(!preg_match('/newwindow/',$_GET['q']) && $node->data[0]["mwbe_vendor"] == 'Yes'){ ?>
        <li><span class="gi-list-item">M/WBE Category:</span> <a href="/contracts_landing<?php echo $status;?>/yeartype/B/year/<?php echo _getFiscalYearID();?>/mwbe/<?php echo $minority_type_id; ?>/dashboard/mp"><?php echo $ethnicity ;?></a></li>
    <?php } else  { ?>
<li><span class="gi-list-item">M/WBE Category: </span><?php echo  $ethnicity ;?></li>
     <?php }
} ?>
</ul>
<?php

if (RequestUtilities::get("datasource") != "checkbook_oge") {
    $querySubVendorinfo = "SELECT SUM(maximum_contract_amount) AS total_current_amt, SUM(original_contract_amount) AS total_original_amt, SUM(rfed_amount) AS total_spent_todate
    FROM {subcontract_details}
    WHERE contract_number = '". $contract_number . "'
    AND latest_flag = 'Y'
    LIMIT 1";

    $results4 = _checkbook_project_execute_sql_by_data_source($querySubVendorinfo,_get_current_datasource());
    if (!isset($res)) {
        $res = new stdClass();
    }
    $res->data = $results4;

    $total_current_amount = $res->data[0]['total_current_amt'];
    $total_original_amount = $res->data[0]['total_original_amt'];
    $total_spent_todate = $res->data[0]['total_spent_todate'];

    $querySubVendorCount = "SELECT  COUNT(DISTINCT vendor_id) AS sub_vendor_count  FROM sub_agreement_snapshot
                            WHERE contract_number = '". $contract_number . "'
                            AND latest_flag = 'Y'
                            LIMIT 1";

    $results3 = _checkbook_project_execute_sql_by_data_source($querySubVendorCount,_get_current_datasource());
    $res->data = $results3;
    $total_subvendor_count = $res->data[0]['sub_vendor_count'];

    $querySubVendorStatus = "SELECT ref_status.scntrc_status_name  AS contract_subvendor_status
                            FROM all_agreement_transactions l1
                            JOIN ref_subcontract_status ref_status on ref_status.scntrc_status = l1.scntrc_status
                            WHERE contract_number = '". $contract_number . "' AND latest_flag = 'Y' LIMIT 1";

    $results6 = _checkbook_project_execute_sql_by_data_source($querySubVendorStatus,_get_current_datasource());
    $res->data = $results6;
    $subVendorStatus = $res->data[0]['contract_subvendor_status'];


}
?>
<?php if(!RequestUtilities::get("datasource") == "checkbook_oge"){?>
<div class="dollar-amounts">
    <h4>
        Sub Vendor Information
    </h4>
    <?php
    if(RequestUtilities::get("doctype")=="CTA1" || RequestUtilities::get("doctype")=="CT1"){
        echo '<ul class="left"><li><span class="gi-list-item">Contract Includes Sub Vendors: </span>'.strtoupper($subVendorStatus).'</li>';
        echo  '<li><span class="gi-list-item">Total Number of Sub Vendors: </span>'.$total_subvendor_count .'</li></ul>';
    }
     ?>
    <div class="spent-to-date">
        <?php if(!preg_match('/newwindow/',$_GET['q'])){ ?>
        <a class="new_window" href="/contract/spending/transactions/contnum/<?php echo $contract_number; echo $status;?>/dashboard/ss/yeartype/B/year/<?php echo CheckbookDateUtil::getCurrentFiscalYearId();?>/syear/<?php echo CheckbookDateUtil::getCurrentFiscalYearId();?>/smnid/721/newwindow"><?php echo custom_number_formatter_format($total_spent_todate, 2, "$");?></a>
        <?php } else {
            echo custom_number_formatter_format($total_spent_todate, 2, "$");?>
        <?php } ?>
        <div class="amount-title">Total Spent
            to Date
        </div>
    </div>
    <div class="original-amount"><?php echo custom_number_formatter_format($total_original_amount, 2, '$');?>
        <div class="amount-title">Total Original
            Amount
        </div>
    </div>
    <div class="current-amount"><?php echo custom_number_formatter_format($total_current_amount, 2, '$');?>
        <div class="amount-title">Total Current
            Amount
        </div>
    </div>
</div>
<?php } ?>
</div>

