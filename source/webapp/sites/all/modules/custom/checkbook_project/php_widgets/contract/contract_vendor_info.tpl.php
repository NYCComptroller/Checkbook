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
if(_getRequestParamValue("magid") != ""){
  $ag_id = _getRequestParamValue("magid");
}else{
  $ag_id = _getRequestParamValue("agid");
}

$queryVendorDetails = "SELECT fa.minority_type_id, fa.contract_number, rb.business_type_code, fa.agreement_id,fa.original_agreement_id,  fa.vendor_id, va.address_id, legal_name AS vendor_name, a.address_line_1, a.address_line_2, a.city, a.state, a.zip, a.country,
	                            (CASE WHEN (rb.business_type_code = 'MNRT' OR rb.business_type_code = 'WMNO') AND vb.status = 2 THEN 'Yes' ELSE 'NO' END) AS mwbe_vendor,
	                            (CASE WHEN fa.minority_type_id in (4,5) then 'Asian American' ELSE fa.minority_type_name END)AS ethnicity
	                        FROM {agreement_snapshot} fa
	                            LEFT JOIN {vendor_history} vh ON fa.vendor_history_id = vh.vendor_history_id
	                            LEFT JOIN {vendor_address} va ON vh.vendor_history_id = va.vendor_history_id
	                            LEFT JOIN {address} a ON va.address_id = a.address_id
	                            LEFT JOIN {ref_address_type} ra ON va.address_type_id = ra.address_type_id
	                            LEFT JOIN {vendor_business_type} vb ON vh.vendor_history_id = vb.vendor_history_id
	                            LEFT JOIN {ref_business_type} rb ON vb.business_type_id = rb.business_type_id
	                            LEFT JOIN {ref_minority_type} rm ON vb.minority_type_id = rm.minority_type_id
	                        WHERE ra.address_type_code = 'PR' and fa.latest_flag = 'Y' and fa.original_agreement_id = " . $ag_id. "LIMIT 1";

$queryVendorCount = " select count(*) total_contracts_sum from {agreement_snapshot} where vendor_id =
(select vendor_id from {agreement_snapshot} where original_agreement_id =". $ag_id . "limit 1)
   and latest_flag = 'Y'";



$results1 = _checkbook_project_execute_sql_by_data_source($queryVendorDetails,_get_current_datasource());
$node->data = $results1;



foreach($node->data as $key => $value){
    if($value['business_type_code'] == "MNRT" || $value['business_type_code'] == "WMNO"){
        $node->data[0]["mwbe_vendor"] = "Yes";
    }
}
$total_cont  = 0;
$results2 = _checkbook_project_execute_sql_by_data_source($queryVendorCount,_get_current_datasource());
//log_error($_SERVER);
foreach($results2 as $row){
    $total_cont +=$row['total_contracts_sum']; 
}
if(_getRequestParamValue("doctype")=="RCT1"){
  $vendor_link = '/contracts_revenue_landing/status/A/year/' . _getCurrentYearID() . '/yeartype/B/vendor/'
                 . $node->data[0]['vendor_id'] . '?expandBottomCont=true';
}
else{
   if($node->data[0]["mwbe_vendor"] == 'Yes'){
       $vendor_link = '/contracts_landing/status/A/year/' . _getCurrentYearID() . '/yeartype/B/vendor/'
        .$node->data[0]['vendor_id'].'/dashboard/mp?expandBottomCont=true';
   }
    else{
        $vendor_link = '/contracts_landing/status/A/year/' . _getCurrentYearID() . '/yeartype/B/vendor/'
            .$node->data[0]['vendor_id'].'?expandBottomCont=true';
    }
}
$contract_number = $node->data[0]['contract_number'];
$querySubVendorCount = "SELECT DISTINCT COUNT(*) AS sub_vendor_count FROM subcontract_details
                        WHERE contract_number = '". $contract_number . "'
                        AND latest_flag = 'Y'
                        LIMIT 1";

$results3 = _checkbook_project_execute_sql_by_data_source($querySubVendorCount,_get_current_datasource());
$res->data = $results3;
$total_subvendor_count = $res->data[0]['sub_vendor_count'];
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
    <li><span class="gi-list-item">Total Number of Sub Vendors:</span> <?php echo $total_subvendor_count; ?></li>
<?php if( _get_current_datasource() == "checkbook" ){?>    
    <li><span class="gi-list-item">M/WBE Vendor:</span> <?php echo $node->data[0]['mwbe_vendor'] ;?></li>
<?php if(!preg_match('/newwindow/',$_GET['q']) && $node->data[0]["mwbe_vendor"] == 'Yes'){ ?>
    <li><span class="gi-list-item">M/WBE Category:</span> <a href="/contracts_landing/status/A/yeartype/B/year/<?php echo _getFiscalYearID();?>/mwbe/<?php echo $minority_type_id; ?>/dashboard/mp"><?php echo $ethnicity ;?></a></li>
<?php } else { ?>
<li><span class="gi-list-item">M/WBE Category: </span><?php echo  $ethnicity ;?></li>
     <?php }
} ?>
</ul>
<?php

$querySubVendorinfo = "SELECT SUM(maximum_contract_amount) AS total_current_amt, SUM(original_contract_amount) AS total_original_amt, SUM(rfed_amount) AS total_spent_todate
FROM {subcontract_details}
WHERE contract_number = '". $contract_number . "'
AND latest_flag = 'Y'
LIMIT 1";

$results4 = _checkbook_project_execute_sql_by_data_source($querySubVendorinfo,_get_current_datasource());
$res->data = $results4;

$total_current_amount = $res->data[0]['total_current_amt'];
$total_original_amount = $res->data[0]['total_original_amt'];
$total_spent_todate = $res->data[0]['total_spent_todate'];
?>
<?php if(!_getRequestParamValue("datasource") == "checkbook_oge"){?>
<div class="dollar-amounts">
    <h4>
        Sub Vendor Information
    </h4>
    <div class="spent-to-date">
        <?php if(!preg_match('/newwindow/',$_GET['q'])){ ?>
        <a target="_blank" href="/contract/spending/transactions/agid/<?php echo $ag_id; ?>/status/A/subvendor/all/dashboard/ss/yeartype/C/year/<?php echo _getCurrentYearID();?>/syear/<?php echo _getCurrentYearID();?>/smnid/721/newwindow"><?php echo custom_number_formatter_format($total_spent_todate, 2, "$");?></a>
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

