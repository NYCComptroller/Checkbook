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