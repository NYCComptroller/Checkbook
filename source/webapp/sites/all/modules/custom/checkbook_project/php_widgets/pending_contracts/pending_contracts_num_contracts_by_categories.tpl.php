<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
?>
<?php
$output = '<h2>Number of Contracts</h2>';
$output .= '<table id="pending-contracts-num-contracts-categories">';
$output .= '<thead><th>Total Number of Contracts</th><th>Master Agreement Contracts</th><th>Standalone Contracts</th></thead>';
$output .= '<tbody><tr><td>'.$node->data[0]['total_num_contracts'].'</td><td>'.$node->data[0]['total_num_master_agreements'].'</td><td>'.$node->data[0]['total_num_standalone_contracts'].'</td></tr></tbody>';
$output .= '</table>';

print $output;

