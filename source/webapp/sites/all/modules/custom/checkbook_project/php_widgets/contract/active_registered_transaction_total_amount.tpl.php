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
<?php
    /*$contactCategory = _getRequestParamValue('contcat');
    $contactCategoryLabel = 'Expense';
    if($contactCategory == 'revenue'){
        $contactCategoryLabel = 'Revenue';
    }*/

    $contactStatus = _getRequestParamValue('contstatus');
    $contactStatusLabel = 'Active';
    if($contactStatus == 'R'){
        $contactStatusLabel = 'Registered';
    }
    if(_checkbook_check_isEDCPage()){
        print '<div class="transactions-total-amount">$'
                  . custom_number_formatter_format($node->data[0]['total_amount_for_transaction'],2)
                  .'<div class="amount-title">Total '.$contactStatusLabel.' Current Contract Amount</div></div>';

    }else if(_checkbook_check_is_mwbe_page() || _getRequestParamValue('dashboard')){
        $current_url = explode('/',$_SERVER['REQUEST_URI']);
        if($current_url[1] == 'contract' && ($current_url[2] == 'search' || $current_url[2] == 'all')&& $current_url[3] == 'transactions'){
            $summaryTitle = "";
        }else{
            $summaryTitle =  'Total '.RequestUtil::getDashboardTitle()." ";
            $summaryTitle = str_replace('Total Total','Total',$summaryTitle);
        }
        print '<div class="transactions-total-amount">$'
            . custom_number_formatter_format($node->data[0]['total_maximum_contract_amount'],2)
            .'<div class="amount-title">'.$summaryTitle.$contactStatusLabel.' Current Contract Amount</div></div>';
    }else{
       print '<div class="transactions-total-amount">$'
          . custom_number_formatter_format($node->data[0]['total_maximum_contract_amount'],2)
          .'<div class="amount-title">Total '.$contactStatusLabel.' Current Contract Amount</div></div>';
    }
