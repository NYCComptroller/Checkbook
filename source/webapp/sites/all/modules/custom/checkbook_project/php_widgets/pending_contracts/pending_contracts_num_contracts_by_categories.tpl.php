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

$output = '<h2>Number of Contracts</h2>';
$output .= '<table id="pending-contracts-num-contracts-categories">';
$output .= '<thead><th>Total Number of Contracts</th><th>Master Agreement Contracts</th><th>Standalone Contracts</th></thead>';
$output .= '<tbody><tr><td>'.$node->data[0]['total_num_contracts'].'</td><td>'.$node->data[0]['total_num_master_agreements'].'</td><td>'.$node->data[0]['total_num_standalone_contracts'].'</td></tr></tbody>';
$output .= '</table>';

print $output;

