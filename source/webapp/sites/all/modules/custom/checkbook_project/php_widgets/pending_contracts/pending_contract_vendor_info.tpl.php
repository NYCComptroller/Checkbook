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
    $contract_num = RequestUtilities::get("contract");
    $version_num = RequestUtilities::get("version");

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




?>
  <ul class="left">
    <li><span class="gi-list-item">Prime Vendor:</span> <a href="<?php echo $vendor_link;?> " ><?php echo $node->data[0]['vendor_name'] ;?></a></li>
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
          $ethnicities[] =MappingUtil::getMinorityCategoryById($minority_type_id);
        }
      }
      $ethnicity = implode(',',$ethnicities);
     if($minority_type_id == "4" || $minority_type_id == "5"){
      $minority_type_id = "4~5";
       }

  ?>
    <li><span class="gi-list-item">Address:</span> <?php echo $address;?></li>
    <li><span class="gi-list-item">Total Number of NYC Contracts:</span> <?php echo $total_cont;?></li>
    <li><span class="gi-list-item">M/WBE Vendor:</span> <?php echo $node->data[0]['mwbe_vendor'] ;?></li>

    <li><span class="gi-list-item">M/WBE Category:</span> <?php echo $ethnicity ;?></li>
</ul>
