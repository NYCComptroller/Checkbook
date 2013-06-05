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
Vendor Information
</h4>
<?php 


//TODO temp fix move bottom code to separate custom preprocess function
if(_getRequestParamValue("magid") != ""){
  $ag_id = _getRequestParamValue("magid");
}else{
  $ag_id = _getRequestParamValue("agid");
}

$queryVendorDetails = "SELECT rb.business_type_code, fa.agreement_id,fa.original_agreement_id,  fa.vendor_id, va.address_id, legal_name AS vendor_name, a.address_line_1, a.address_line_2, a.city, a.state, a.zip, a.country,
	                            (CASE WHEN (rb.business_type_code = 'MNRT' OR rb.business_type_code = 'WMNO') THEN 'Yes' ELSE 'NO' END) AS mwbe_vendor,
	                            (CASE WHEN rm.minority_type_id in (4,5) then 'Asian American' ELSE rm.minority_type_name END)AS ethnicity
	                        FROM {agreement_snapshot} fa
	                            LEFT JOIN {vendor_history} vh ON fa.vendor_history_id = vh.vendor_history_id
	                            LEFT JOIN {vendor_address} va ON vh.vendor_history_id = va.vendor_history_id
	                            LEFT JOIN {address} a ON va.address_id = a.address_id
	                            LEFT JOIN {ref_address_type} ra ON va.address_type_id = ra.address_type_id
	                            LEFT JOIN {vendor_business_type} vb ON vh.vendor_history_id = vb.vendor_history_id
	                            LEFT JOIN {ref_business_type} rb ON vb.business_type_id = rb.business_type_id
	                            LEFT JOIN {ref_minority_type} rm ON vb.minority_type_id = rm.minority_type_id
	                        WHERE ra.address_type_code = 'PR' and fa.latest_flag = 'Y' and fa.original_agreement_id = " . $ag_id;

$queryVendorCount = " select count(*) total_contracts_sum from {agreement_snapshot} where vendor_id =
(select vendor_id from {agreement_snapshot} where original_agreement_id =". $ag_id . "limit 1)
   and latest_flag = 'Y'";

$results1 = _checkbook_project_execute_sql($queryVendorDetails);
$node->data = $results1;
foreach($node->data as $key => $value){
    if($value['business_type_code'] == "MNRT" || $value['business_type_code'] == "WMNO"){
        $node->data[0]["mwbe_vendor"] = "Yes";
    }
}
$total_cont  = 0;
$results2 = _checkbook_project_execute_sql($queryVendorCount);
//log_error($_SERVER);
foreach($results2 as $row){
    $total_cont +=$row['total_contracts_sum']; 
}
if(_getRequestParamValue("doctype")=="RCT1"){
  $vendor_link = '/contracts_revenue_landing/status/A/year/' . _getCurrentYearID() . '/yeartype/B/vendor/'
                 . $node->data[0]['vendor_id'] . '?expandBottomCont=true';
}
else{
   $vendor_link = '/contracts_landing/status/A/year/' . _getCurrentYearID() . '/yeartype/B/vendor/'
                  .$node->data[0]['vendor_id'].'?expandBottomCont=true';
}
  

?>
  <ul class="left">
    <li><span class="gi-list-item">Vendor:</span> <a href="<?php echo $vendor_link;?> " ><?php echo $node->data[0]['vendor_name'] ;?></a></li>
  <?php 
      $address = $node->data[0]['address_line_1'] ;
      $address .= " "  .  $node->data[0]['address_line_2'];
      $address .= " "  .  $node->data[0]['city'];
      $address .= " "  .  $node->data[0]['state'];
      $address .= " "  .  $node->data[0]['zip'];
      $address .= " "  .  $node->data[0]['country'];
      
      $ethnicities = array();
      foreach($node->data as $row){
        if($row['ethnicity'] != null and trim($row['ethnicity']) != '' ){
          $ethnicities[] = $row['ethnicity'];
        }
      } 
      $ethnicity = implode(',',$ethnicities);
      
  ?>    
    <li><span class="gi-list-item">Address:</span> <?php echo $address;?></li>
    <li><span class="gi-list-item">Total Number of NYC Contracts:</span> <?php echo $total_cont;?></li>
    <li><span class="gi-list-item">M/WBE Vendor:</span> <?php echo $node->data[0]['mwbe_vendor'] ;?></li>
    
    <li><span class="gi-list-item">Ethnicity:</span> <?php echo $ethnicity ;?></li>
</ul>