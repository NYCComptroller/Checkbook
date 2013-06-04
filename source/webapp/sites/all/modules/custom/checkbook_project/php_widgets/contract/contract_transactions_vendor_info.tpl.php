<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
?>
<?php
$output = '';
foreach($node->data as $key=>$value){
$output .= '<div class="field-label">Vendor Name: </div><div class="field-items">'. $value['vendor_name'].'</div>'.
           '<div class="field-label">Total number of Contracts: </div><div class="field-items">'. ''.'</div>'.
           '<div class="field-label">Address: </div><div class="field-items">'. $value['address_line_1']. $value['address_line_2']. '<br/>'. $value['city']. ' '.$value['state']. ' ' .$value['zip']. ' '.$value['country'].'</div>'.
           '<div class="field-label">M/WBE Vendor: </div><div class="field-items">'. $value['mwbe_vendor'].'</div>'.
           '<div class="field-label">Ethnicity: </div><div class="field-items">'. $value['ethnicity'].'</div>'.
           '<div class="field-label">Vendor Hold: </div><div class="field-items">'. ''.'</div>';

}
print $output;