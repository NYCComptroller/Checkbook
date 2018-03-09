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

    $year1 = _getYearValueFromID(_getRequestParamValue('year'));
    $year2 = _getYearValueFromID(_getRequestParamValue('year')+1);
    $year3 = _getYearValueFromID(_getRequestParamValue('year')+2);
    $total_revenue_recognized_yr1 = custom_number_formatter_format($node->data[0]['current_recognized'],2,'$');
    $total_revenue_recognized_yr2 = custom_number_formatter_format($node->data[0]['recognized_1'],2,'$');
    $total_revenue_recognized_yr3 = custom_number_formatter_format($node->data[0]['recognized_2'],2,'$');
    $total_others = custom_number_formatter_format($node->data[0]['other_years'],2,'$');
    $total_remaining_budget = custom_number_formatter_format($node->data[0]['remaining_amount'],2,'$');

    print '<div class="dollar-amounts">';
    print '<div class="total-spending-amount">'.$total_revenue_recognized_yr3."<div class='amount-title'>Total Revenue Recognized ".$year3."</div>".'</div>';
    print '<div class="total-spending-amount">'.$total_revenue_recognized_yr2."<div class='amount-title'>Total Revenue Recognized ".$year2."</div>".'</div>';
    print '<div class="total-spending-amount">'.$total_revenue_recognized_yr1."<div class='amount-title'>Total Revenue Recognized ".$year1."</div>" .'</div>' ;
    print '<div class="total-spending-amount">'.$total_others."<div class='amount-title'>Total Others</div>" .'</div>' ;
    print '<div class="total-spending-amount">'.$total_remaining_budget."<div class='amount-title'>Total Remaining<br />Budget</div>" .'</div>' ;
    print '</div>';